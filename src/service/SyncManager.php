<?php


namespace durino13\service;


use DateTime;
use Throwable;
use DateTimeZone;
use durino13\model\jira\JiraIssue;
use durino13\model\toggl\TimeEntry;
use durino13\model\toggl\TogglProject;
use durino13\model\UpdateModel;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use MorningTrain\TogglApi\TogglApi;
use GuzzleHttp\Exception\GuzzleException;

class SyncManager
{

    protected $togglService;

    protected $togglProjects;

    protected $jiraIssues;

    protected $allTimeEntries;

    protected $jiraIssuesWithNoWorkLog;

    protected $updateModel;

    public function __construct(TogglApi $togglService)
    {
        $this->togglService = $togglService;
    }

    /**
     * Load data from JIRA and Toggl and pair them
     * @param int $clientId
     * @param $daysAgo
     * @return SyncManager
     * @throws \Exception
     */
    public function initialize(int $clientId, $daysAgo): SyncManager
    {
        $this->loadAllJiraProjects();
        $this->loadTogglActiveClientProjects($clientId);
        $this->getAllTogglTimeEntriesDaysAgo($daysAgo);
        return $this;
    }

    public function loadAllJiraProjects()
    {
        $client = new Client();
        $response = $client->request('POST', getenv('JIRA_WORKLOG_URL'), [
            'auth' => [getenv('JIRA_USER'), getenv('JIRA_PASS')],
            RequestOptions::JSON => [
                'jql' => 'assignee = juraj.marusiak AND status in ("Test", "Revised", "For revision") AND created >= -4w'
            ],
        ]);

        $jiraIssues = json_decode((string)$response->getBody());

        if (!isset($jiraIssues->issues)) {
            throw new Exception('Cant load JIRA issues');
        }

        foreach ($jiraIssues->issues as $issue) {
            $jiraIssue = new JiraIssue();
            $jiraIssue->setIssueName($issue->key);
            $jiraIssue->setTimeSpend($issue->fields->timespent);
            $jiraIssue->setIssueId($issue->id);
            $this->jiraIssues[] = $jiraIssue;
        }
    }

    public function  findJiraSubtasksWithNoWorkLog()
    {
        $this->jiraIssuesWithNoWorkLog = array_filter($this->jiraIssues, function($jiraIssue) {
            /** @var JiraIssue $jiraIssue */
           return $jiraIssue->getTimeSpend() === null || $jiraIssue->getTimeSpend() === 0;
        });
        return $this;
    }

    public function getWorkLogData()
    {;
        /** @var JiraIssue $jiraSubtaskWithNoWorkLog */
        foreach ($this->jiraIssuesWithNoWorkLog as $jiraSubtaskWithNoWorkLog) {
            /** @var TimeEntry $timeEntry */
            foreach ($this->allTimeEntries as $timeEntry) {
                if ($timeEntry->getToggleProject()->getName() === $jiraSubtaskWithNoWorkLog->getIssueName()) {
                    try {
                        $updateModel = new UpdateModel();
                        $updateModel->setJiraId($jiraSubtaskWithNoWorkLog->getIssueId());
                        $updateModel->setJiraKey($jiraSubtaskWithNoWorkLog->getIssueName());
                        $updateModel->setDuration($timeEntry->getDuration());
                        $this->updateModel[] = $updateModel;
                    } catch (Exception $e) {
                        echo $e->getMessage() . PHP_EOL;
                    }
                }
            }
        }
        return $this;
    }

    public function sendWorkLogToJira(): void
    {
        $client = new Client();
        /** @var UpdateModel $updateModel */

        if (!isset($this->updateModel)) {
            echo 'Notting to log' . PHP_EOL;
            return;
        }

        foreach ($this->updateModel as $updateModel) {
            try {
                $client->request('POST', getenv('JIRA_HOST') . '/rest/api/3/issue/' . $updateModel->getJiraId() . '/worklog', [
                    'auth' => [getenv('JIRA_USER'), getenv('JIRA_PASS')],
                    RequestOptions::JSON => [
                        'timeSpentSeconds' => $updateModel->getDuration(),
                    ],
                ]);
                echo 'Logged "' . gmdate('H:i:s', $updateModel->getDuration()) . '" seconds to ' . $updateModel->getJiraKey() . PHP_EOL;
            } catch (GuzzleException $e) {
                echo 'Nepodarilo sa zapisat cas pre jiru: ' . $updateModel->getJiraKey();
            }
        }
    }

    /**
     * Load projects from Toggl
     * @param int $clientId
     * @return $this
     */
    public function loadTogglActiveClientProjects(int $clientId)
    {
        $this->togglProjects = $this->togglService->getActiveClientProjects($clientId);
        return $this;
    }

    /**
     * @param int $daysAgo
     * @return bool|mixed|object
     * @throws \Exception
     */
    public function getAllTogglTimeEntriesDaysAgo(int $daysAgo) {
        $datetimeFormat = 'Y-m-d\TH:i:s.000\Z';
        $intervalSpec = 'P' . $daysAgo . 'D';
        $today = new DateTime('now', new DateTimeZone('CET'));
        $today->setTime(23,59,59);
        $yesterday = new DateTime('now', new DateTimeZone('CET'));
        $yesterday = $yesterday->sub(new \DateInterval($intervalSpec));
        $yesterday->setTime(0,0,1);
        $stdTimeEntries = $this->togglService->getTimeEntriesInRange($yesterday->format($datetimeFormat), $today->format($datetimeFormat));

        foreach ($stdTimeEntries as $stdTimeEntry) {
            try {
                if (isset($stdTimeEntry->pid)) {
                    $timeEntry = new TimeEntry();
                    $timeEntry->setId($stdTimeEntry->id);
                    $timeEntry->setPid($stdTimeEntry->pid);
                    $timeEntry->setDuration($stdTimeEntry->duration);
                    $description = $stdTimeEntry->description ?? '';
                    $timeEntry->setDescription($description);// Find toggle project
                    $timeEntry->addToggleProject($this->createToggleProject($timeEntry->getPid()));
                    $this->allTimeEntries[] = $timeEntry;
                }
            } catch (Throwable $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

    public function createToggleProject(int $projectId): TogglProject
    {
        $res = array_values(array_filter($this->togglProjects, static function($project) use ($projectId) {
            return $project->id === $projectId;
        }));

        if (!isset($res[0])) {
            throw new Exception('Cant find toggle project with ID: ' . $projectId);
        }

        $togglProject = new TogglProject();
        $togglProject->setId($res[0]->id);
        $togglProject->setName($res[0]->name);
        $togglProject->setActive($res[0]->active);
        $togglProject->setAt($res[0]->at);
        $togglProject->setCreatedAt($res[0]->created_at);

        return $togglProject;
    }

}

<?php


namespace durino13\model\jira;


class JiraIssue
{

    protected $issueId;

    protected $issueName;

    protected $timeSpend;

    /**
     * @return mixed
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * @param mixed $issueId
     */
    public function setIssueId($issueId): void
    {
        $this->issueId = $issueId;
    }

    /**
     * @return mixed
     */
    public function getIssueName()
    {
        return $this->issueName;
    }

    /**
     * @param mixed $issueName
     */
    public function setIssueName($issueName): void
    {
        $this->issueName = $issueName;
    }

    /**
     * @return mixed
     */
    public function getTimeSpend()
    {
        return $this->timeSpend;
    }

    /**
     * @param mixed $timeSpend
     */
    public function setTimeSpend($timeSpend): void
    {
        $this->timeSpend = $timeSpend;
    }

}

<?php


namespace durino13\model;


class UpdateModel
{

    protected $jiraId;

    protected $jiraKey;

    protected $duration;

    /**
     * @return mixed
     */
    public function getJiraId()
    {
        return $this->jiraId;
    }

    /**
     * @param mixed $jiraId
     */
    public function setJiraId($jiraId): void
    {
        $this->jiraId = $jiraId;
    }

    /**
     * @return mixed
     */
    public function getJiraKey()
    {
        return $this->jiraKey;
    }

    /**
     * @param mixed $jiraKey
     */
    public function setJiraKey($jiraKey): void
    {
        $this->jiraKey = $jiraKey;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

}

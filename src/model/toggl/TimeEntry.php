<?php


namespace durino13\model\toggl;


class TimeEntry
{

    protected $id;
    protected $pid;
    protected $duration;
    protected $description;
    protected $toggleProject;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $pid
     */
    public function setPid($pid): void
    {
        $this->pid = $pid;
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

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @param TogglProject $togglProject
     */
    public function addToggleProject(TogglProject $togglProject): void
    {
        $this->toggleProject = $togglProject;
    }

    /**
     * @return mixed
     */
    public function getToggleProject(): TogglProject
    {
        return $this->toggleProject;
    }

}

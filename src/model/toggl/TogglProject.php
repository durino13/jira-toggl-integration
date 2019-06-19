<?php


namespace durino13\model\toggl;


class TogglProject
{

    protected $id;
    protected $name;
    protected $active;
    protected $at;
    protected $created_at;
    protected $timeEntries;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getAt()
    {
        return $this->at;
    }

    /**
     * @param mixed $at
     */
    public function setAt($at): void
    {
        $this->at = $at;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @param TimeEntry $timeEntry
     */
    public function addTimeEntry(TimeEntry $timeEntry)
    {
        $this->timeEntries[] = $timeEntry;
    }
    
}

<?php

namespace Entvalley\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Entvalley\AppBundle\Domain\Status;
use HTMLPurifier;

class Task
{
    private $id;
    private $title;
    private $body;
    private $author;
    private $assignedTo;
    private $createdAt;
    private $lastModification;
    private $comments;
    private $project;
    private $lastStatus;
    private $status;
    private $numberComments = 0;
    private $safeBody = '';

    /**
     * @var $htmlPurifier HTMLPurifier
     */
    private $htmlPurifier;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->status = Status::UNASSIGNED;
    }

    public function setAssignedTo($assignedTo = null)
    {
        $this->assignedTo = $assignedTo;
    }

    public function getAssignedTo()
    {
        return $this->assignedTo;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLastModification($lastModification)
    {
        $this->lastModification = $lastModification;
    }

    public function getLastModification()
    {
        return $this->lastModification;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        $this->numberComments++;
    }

    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Splits the provided text by a new line character and interpret the first line as
     * a task title and the rest as a task body
     *
     * @param $text
     */
    public function setBodyWithTitle($text)
    {
        $textParts = preg_split("~(\n|\r)~", $text, 2);

        $this->title = trim($textParts[0]);
        $this->body = isset($textParts[1]) ? trim($textParts[1]) : "";
    }

    /**
     * Changes the status of the task. All status changes are recorded and must belong to a
     * specific user.
     *
     * @see Task::getLastStatus
     * @param User $whoUpdated
     * @param $status
     */
    public function setStatus(User $whoUpdated, $status)
    {
        $statusChange = new StatusChange();
        $statusChange->setTask($this);
        $statusChange->setStatus($status);
        $statusChange->setWhoUpdated($whoUpdated);
        $this->status = $status;
        $this->lastStatus = $statusChange;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return StatusChange
     */
    public function getLastStatus()
    {
        return $this->lastStatus;
    }

    public function getNumberComments()
    {
        return $this->numberComments;
    }

    /**
     * Serializer callback to generate a body with safe HTML tags which can be displayed
     * in the browser. Requires a link to html purifier service.
     *
     * @see Task::setHtmlPurifier
     */
    public function purifyHtmlTags()
    {
        if ($this->htmlPurifier) {
            $this->safeBody = $this->htmlPurifier->purify($this->body);
        }
    }

    public function setHtmlPurifier($htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;
    }

    public function getProject()
    {
        return $this->project;
    }
}
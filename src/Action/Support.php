<?php
/**
 * Laika Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

namespace LBM\Action;

use Laika\Core\Http\Request;
use Laika\Core\Http\Redirect;
use Laika\App\Model\SupportTicketModel;
use Laika\App\Model\SupportTicketTagModel;
use Laika\App\Model\SupportDepartmentModel;
use Laika\App\Model\SupportTicketStatusModel;
use Laika\App\Model\SupportTicketReplyModel;
use Laika\App\Model\SupportCannedResponseModel;

class Support
{
    /** @var Request $request */
    protected Request $request;

    /** @var Redirect $redirect */
    protected Redirect $redirect;

    /** @var SupportTicketModel $model */
    protected SupportTicketModel $model;

    /** @var SupportTicketTagModel $tag_model */
    protected SupportTicketTagModel $tag_model;

    /** @var SupportTicketStatusModel $status_model */
    protected SupportTicketStatusModel $status_model;

    /** @var SupportDepartmentModel $dep_model */
    protected SupportDepartmentModel $dep_model;

    /** @var SupportTicketReplyModel $reply_model */
    protected SupportTicketReplyModel $reply_model;

    /** @var SupportCannedResponseModel $can_model */
    protected SupportCannedResponseModel $can_model;

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    public function __construct()
    {
        $this->request = new Request();
        $this->redirect = new Redirect();
        $this->model = new SupportTicketModel();
        $this->tag_model = new SupportTicketTagModel();
        $this->status_model = new SupportTicketStatusModel();
        $this->dep_model = new SupportDepartmentModel();
        $this->reply_model = new SupportTicketReplyModel();
        $this->can_model = new SupportCannedResponseModel();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
    }
}
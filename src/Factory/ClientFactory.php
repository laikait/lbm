<?php

/**
 * Cloud Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

// Namespace
namespace LBM\Factory;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

use Laika\App\Model\ClientStatus;
use Laika\App\Model\ClientNote;
use Laika\App\Model\Security;
use Laika\App\Model\Country;
use Laika\App\Model\Address;
use Laika\App\Model\Client;
use Laika\App\Model\Staff;
use LBM\Support\ChangeLog;
use LBM\Support\FormError;
use LBM\Abstract\Factory;
use LANG;

class ClientFactory extends Factory
{
    /**
     * Initiate Client Factory
     */
    public function __construct()
    {
        parent::__construct('Client', ['id', 'uuid', 'fname', 'lname', 'username', 'email', 'status', 'country', 'companyname']);
    }

    /**
     * Get Single Client
     * @param int|string $entity Entity to Get Value.
     * @return array
     */
    public function first(int|string $entity): array
    {
        $entity = \htmlspecialchars($entity);
        $where = [
            'id'        =>  $entity,
            'uuid'      =>  $entity,
            'username'  =>  $entity,
            'email'     =>  $entity
        ];

        // Get Client
        $client = $this->model->where($where, '=', 'OR')->first();

        // Get Other Related Values
        if (!empty($client)) {
            // Get Status
            $client['status'] = (new ClientStatus())->select('entity,color')->where(['entity' => $client['status']])->first();

            // Client Notes
            $client['notes'] = (new ClientNote())->select('note,staff,created')->where(['relid' => $client['id']])->order('id', 'DESC')->get();

            // Get Address
            $client['address'] = (new Address())->select('address_1,address_2,city,state,zip,country')->where(['type' => 'client', 'relid' => $client['id']])->first();

            // Get Note Staffs
            $staff = new Staff();
            foreach ($client['notes'] as $k => $note) {
                $client['notes'][$k]['staff'] = $staff->select('uuid,username')->where(['id' => $note['staff']])->first();
            }
        }

        return $client;
    }

    /**
     * Get Limit Clients
     */
    public function limit(): array
    {
        // Get Input
        $input = \do_hook('request.input', 'client');

        // Get Model Object for Total Clients
        $total = (new Client())->select($this->model->id);
        if (!empty($input)) {
            $input = "^{$input}";
            $where = [
                'fname' => $input,
                'lname' => $input,
                'username' => $input,
                'email' => $input,
                'status' => $input,
                'country' => $input,
                'companyname' => $input
            ];
            // Extend Total Client Model
            $total = $total->where($where, 'REGEXP', 'OR');
            // Extend Client Model
            $this->model = $this->model->where($where, 'REGEXP', 'OR');
        } else {
            // Extend Total Client Model
            $total = $total->where($this->queries());
            // Extend Client Model
            $this->model = $this->model->where($this->queries());
        }

        // Return Result
        $clients = $this->model->select()->limit($this->limit)->offset($this->page)->get();
        // Set Total Client
        $this->total = $total->count();

        // Get Related Values
        if (!empty($clients)) {
            $smodel = new ClientStatus();
            foreach ($clients as $k => $client) {
                // Get Status
                $clients[$k]['status'] = $smodel->select('entity,color')->where(['entity' => $client['status']])->first();
            }
        }

        return $clients;
    }

    /**
     * Create New Client
     * @return ?array
     */
    public function create(): ?array
    {
        // Check Staff Has Access
        if (!admin_access('client.create')) {
            return ['status' => false, 'message' => LANG::$permissionDenied];
        }
        // Get Inputs
        $inputs = $this->request->inputs();
        show($inputs, true);

        return null;
    }

    /**
     * Get Total Client
     * @return int
     */
    public function total(): int
    {
        return $this->total ?? 0;
    }

    /**
     * Update on Request
     * @param array $client Single Client Details
     * @return ?array
     */
    public function updateOnRequest(array $client): ?array
    {
        // Check Staff Has Access
        if (!admin_access('client.update')) {
            return ['status' => false, 'message' => LANG::$permissionDenied];
        }
        // Get Inputs
        $inputs = $this->request->inputs();

        // Validate UUID
        if ($client['uuid'] !== $inputs['uuid']) {
            return ['status' => false, 'message' => LANG::$generalError];
        }

        // Get Statuses & Countries List
        $statuses = call_user_func([new ClientStatus(), 'list']);
        $countries = call_user_func([new Country(), 'list']);

        // Validate & Update Data
        $this->request->validate([
            'fname' => 'required|min:3|max:50',
            'lname' => 'required|min:3|max:50',
            'email' => 'required|email',
            'status' => 'required|in:' . implode(',', array_keys($statuses)),
            'address_1' => 'required|min:1|max:255',
            'country' => 'required|in:' . implode(',', array_keys($countries)),
        ], [
            'fname.required' => LANG::$requiredField,
            'fname.min' => sprintf(LANG::$minLength, 3),
            'fname.max' => sprintf(LANG::$maxLength, 50),
            'email.required' => LANG::$requiredField,
            'email.email' => LANG::$invalidEmail,
            'lname.required' => LANG::$requiredField,
            'lname.min' => sprintf(LANG::$minLength, 3),
            'lname.max' => sprintf(LANG::$maxLength, 50),
            'status.required' => LANG::$requiredField,
            'status.in' => LANG::$invalidOption,
            'address_1.required' => LANG::$requiredField,
            'address_1.min' => sprintf(LANG::$minLength, 1),
            'address_1.max' => sprintf(LANG::$maxLength, 255),
        ]);

        // Set Form Errors
        FormError::add($this->request->errors());

        // Return if Form Has Error
        if (FormError::hasError()) {
            return null;
        }

        $clientInput = [
            'fname' => $inputs['fname'],
            'lname' => $inputs['lname'],
            'email' => $inputs['email'],
            'status' => $inputs['status'],
            'country' => $inputs['country']
        ];

        $existingClientInfo = [
            'fname' => $client['fname'],
            'lname' => $client['lname'],
            'email' => $client['email'],
            'status' => $client['status']['entity'],
            'country' => $client['address']['country']
        ];

        // Update Address
        $addressInput = [
            'address_1' => $inputs['address_1'],
            'address_2' => $inputs['address_2'] ?? NULL,
            'state' => $inputs['state'] ?? NULL,
            'city' => $inputs['city'] ?? NULL,
            'zip' => $inputs['zip'] ?? NULL,
            'country' => $inputs['country']
        ];
        $existingAddress = $client['address'];

        // Check Has Changes
        $clientLog = new ChangeLog($existingClientInfo, $clientInput);
        $addressLog = new ChangeLog($existingAddress, $addressInput);

        // Update Client Info & Address if Log Not Empty
        $logs = array_merge($clientLog->logs(), $addressLog->logs());

        // Take Action if Has Change Logs
        if (!empty($logs)) {
            $staff = staff();
            // Make New Log Data
            $activity = [
                'relid' => $staff['id'],
                'task'  => LANG::$updatedClient,
                'activity' => sprintf(
                        LANG::$clientUpdatedByStaff,
                        \do_hook('log.url', $client['username'], 'staff.client', ['client' => $client['uuid']]),
                        \do_hook('log.url', $staff['username'], 'staff.staff', ['staff' => $staff['uuid']]),
                    ),
                'changes' => serialize($logs),
                'created' => \do_hook('date.format')
            ];

            try {
                // Add Updated Column
                $clientInput['updated'] = $addressInput['updated'] = \do_hook('date.format');

                // Update Client
                $this->update(['uuid' => $client['uuid']], $clientInput);
                
                // Set Address Where
                $addressWhere = ['type' => 'client', 'relid' => $client['id'], 'profile_default' => 'yes'];
                // Update Address
                call_user_func([new AddressFactory, 'update'], $addressWhere, $addressInput);

                // Update Log
                call_user_func([new StaffFactory, 'createActivity'], $activity);

                return ['status' => true, 'message' => LANG::$clientUpdatedSuccessfully];
            } catch (\Throwable $th) {
                // Set Failed Message
                $message = \do_hook('redirect.message', LANG::$createActivityFailed, $th->getMessage());
                return ['status' => true, 'message' => $message];
            }
        }
        return ['status' => true, 'message' => LANG::$noChanges];
    }

    /**
     * Reset Password on Request
     * @param array $client Single Client Details
     * @return array
     */
    public function resetPassword(array $client): array
    {
        // Check Staff Has Access
        if (!admin_access('client.update')) {
            return ['status' => false, 'message' => LANG::$permissionDenied];
        }

        // Get Inputs
        $inputs = $this->request->inputs();

        // Validate UUID
        if ($client['uuid'] !== $inputs['uuid']) {
            return ['status' => false, 'message' => LANG::$generalError];
        }

        $data = ['reset_token' => bin2hex(random_bytes(32)), 'token_expire' => time()];
        try {
            $this->update(['uuid' => $client['uuid']], $data);
        } catch (\Throwable $th) {
            $message = \do_hook('redirect.message', LANG::$resetPasswordFailed, $th->getMessage());
            return ['status' => true, 'message' => $message];
        }
        return ['status' => true, 'message' => LANG::$resetPasswordSuccessful];
    }

    /**
     * Reset Security Code on Request
     * @param array $client Single Client Details
     * @return array
     */
    public function resetSecurityCode(array $client): array
    {
        // Check Staff Has Access
        if (!admin_access('client.update')) {
            return ['status' => false, 'message' => LANG::$permissionDenied];
        }

        // Get Inputs
        $inputs = $this->request->inputs();

        // Validate UUID
        if ($client['uuid'] !== $inputs['uuid']) {
            return ['status' => false, 'message' => LANG::$generalError];
        }

        try {
            $obj = new Security();
            $where = ['client' => $client['id'], 'entity' => 'code'];
            $obj->where($where)->update(['data' => mt_rand(100000, 999999)]);
        } catch (\Throwable $th) {
            $message = \do_hook('redirect.message', LANG::$resetSecurityCodeFailed, $th->getMessage());
            return ['status' => false, 'message' => $message];
        }
        return ['status' => true, 'message' => LANG::$resetSecurityCodeSuccessful];
    }

    /**
     * Add New Note on Request
     * @param array $client Single Client Details
     * @return array
     */
    public function addNewNote(array $client): array
    {
        // Check Staff Has Access
        if (!admin_access('note.create')) {
            return [
                'status' => false,
                'message' => LANG::$permissionDenied
            ];
        }

        // Get Inputs
        $inputs = $this->request->inputs();

        // Validate UUID
        if ($client['uuid'] !== $inputs['uuid']) {
            return [
                'status' => false,
                'message' => LANG::$generalError
            ];
        }

        // Validate & Update Data
        $this->request->validate(['note' => 'required|min:2'], ['note.min' => sprintf(LANG::$minLength, 1)]);

        // Set Form Errors
        if (!empty($this->request->errors())) {
            return ['status' => false, 'message' => \do_hook('form.error', 'note')];
        }

        // Insert Note
        try {
            $obj = new ClientNote();
            $data = [
                'uuid' => $obj->uuid(),
                'relid' => $client['id'],
                'staff' => staff()['id'],
                'note' => $inputs['note'],
                'created' => \do_hook('date.format')
            ];

            $obj->transaction(function ($m) use($data) {
                $m->insert($data);
            });
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => \do_hook('redirect.message', LANG::$addNoteFailed, $th->getMessage())
                ];
        }
        return ['status' => true, 'message' => LANG::$addNoteSuccess];
    } 
}
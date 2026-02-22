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

use Laika\App\Model\StaffActivity;
use Laika\App\Model\ClientNote;
use Laika\Core\Http\FormError;
use Laika\Core\Http\ChangeLog;
use Laika\App\Model\Security;
use Laika\App\Model\Address;
use Laika\Core\Helper\Date;
use Laika\Core\Regex\Regex;
use LBM\Abstract\Factory;
use LANG;

class ClientFactory extends Factory
{
    /**
     * Initiate Client Factory
     */
    public function __construct()
    {
        parent::__construct('Client', ['id', 'uid', 'fname', 'lname', 'username', 'email', 'status', 'country', 'companyname']);
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

        // Get Countries List
        $countries = \do_hook('country.list');

        // Validate & Update Data
        $rules = [
            'fname' => 'required|min:1|max:50',
            'lname' => 'required|min:1|max:50',
            'username' => 'required|min:6|max:50|regex:/^[a-zA-Z0-9]+$/i',
            'password' => 'required',
            'cpassword' => 'required|match:password',
            'email' => 'required|email',
            'address_1' => 'required|min:1|max:255',
            'country' => 'required|in:' . implode(',', array_keys($countries)),
        ];
        $rules_messages = [
            'fname.required' => LANG::$requiredField,
            'fname.min' => sprintf(LANG::$minLength, 1),
            'fname.max' => sprintf(LANG::$maxLength, 50),
            'lname.required' => LANG::$requiredField,
            'lname.min' => sprintf(LANG::$minLength, 1),
            'lname.max' => sprintf(LANG::$maxLength, 50),
            'username.required' => LANG::$requiredField,
            'username.min' => sprintf(LANG::$minLength, 6),
            'username.max' => sprintf(LANG::$maxLength, 50),
            'username.regex' => LANG::$unsupportedCharacter,
            'password.required' => LANG::$requiredField,
            'password.min' => sprintf(LANG::$minLength, 6),
            'cpassword.required' => LANG::$requiredField,
            'cpassword.min' => sprintf(LANG::$minLength, 6),
            'cpassword.match' => LANG::$cpasswordNotMatchd,
            'email.required' => LANG::$requiredField,
            'email.email' => LANG::$invalidEmail,
            'address_1.required' => LANG::$requiredField,
            'address_1.min' => sprintf(LANG::$minLength, 1),
            'address_1.max' => sprintf(LANG::$maxLength, 255),
        ];

        $this->request->validate($rules, $rules_messages);

        // Set Form Errors
        FormError::addBulk($this->request->errors());

        // Check User Doesn't Exists
        $username = $this->request->input('username');
        if ($this->model->select('username')->where(['username' => $username])->first()) {
            FormError::add('username', LANG::$alreadyExists);
        }
        if ($this->model->select('email')->where(['email' => $username])->first()) {
            FormError::add('email', LANG::$alreadyExists);
        }

        $password = $this->request->input('password');
        $password_char_limit = \do_hook('option.int', 'password.char.limit', 6);

        // Validate Password
        $regex = new Regex();
        if (!$regex->validate('minimum', $password, $password_char_limit)) {
            FormError::add('password', sprintf(LANG::$minLength, $password_char_limit));
        }
        if (\do_hook('option.bool', 'password.upper.required', false)) {
            if (!$regex->validate('hasupper', $password)) {
                FormError::add('password', LANG::$upperCharRequired);
            }
        }
        if (\do_hook('option.bool', 'password.lower.required', false)) {
            if (!$regex->validate('haslower', $password)) {
                FormError::add('password', LANG::$lowerCharRequired);
            }
        }
        if (\do_hook('option.bool', 'password.numeric.required', false)) {
            if (!$regex->validate('hasnumeric', $password)) {
                FormError::add('password', LANG::$numericRequired);
            }
        }
        if (\do_hook('option.bool', 'password.special.required', false)) {
            if (!$regex->validate('hasspecial', $password)) {
                FormError::add('password', LANG::$specialCharRequired);
            }
        }

        // Return if Form Has Error
        if (FormError::exists()) {
            return null;
        }

        // Insert Client
        try {
            $this->model->transaction(function ($m) {
                // Make Date Object
                $date = new Date(timezone:\do_hook('option', 'time.zone'));

                // Get Inputs
                $inputs = $this->request->inputs();

                // Get Insert User Data
                $user_data = [
                    'uid' => $this->model->uid(),
                    'fname' => $inputs['fname'],
                    'lname' => $inputs['lname'],
                    'username' => $inputs['username'],
                    'email' => $inputs['email'],
                    'lname' => $inputs['lname'],
                    'companyname' => $inputs['companyname'] ?? NULL,
                    'hash' => bin2hex(random_bytes(64)),
                    'email_verify_token' => bin2hex(random_bytes(32)),
                    'token_expire' => $date->setTimestamp(time() + 900)->format(),
                    'status' => 'inactive',
                    'emailstatus' => 'unverified',
                    'country' => $inputs['country'],
                    'created' => $date->setTimestamp(time())->format()
                ];

                // Insert & Get Client ID
                $id = $m->insert($user_data);

                // Throw Error if Client Insertion Failed
                if ($id === false) {
                    throw new \Exception("Insert Client Failed!");
                }

                // Get Address Data
                $address_data = [
                    'address_1' => $inputs['address_1'],
                    'address_2' => $inputs['address_2'],
                    'state' => $inputs['state'] ?? NULL,
                    'city' => $inputs['city'] ?? NULL,
                    'zip' => $inputs['zip'] ?? NULL,
                    'country' => $inputs['country'],
                    'type' => 'client',
                    'relid' => $id,
                    'profile_default' => 'yes',
                    'created' => $date->format()
                ];

                // Get Security Data
                $security_data = [
                    'client' => $id,
                    'entity' => 'code',
                    'data' => mt_rand(100000, 999999)
                ];

                // Insert Address & Security
                call_user_func([new Address(), 'insert'], $address_data);
                call_user_func([new Security(), 'insert'], $security_data);

            });
            // Return Success Message
            return ['status' => true, 'message' => LANG::$addClientSuccess];
        } catch (\Throwable $th) {
            $message = \do_hook('redirect.message', LANG::$addClientFailed, $th->getMessage());
            return ['status' => false, 'message' => $message];
        }

        return null;
    }

    /**
     * Update on Request
     * @param array $client Single Client Details
     * @return ?array
     */
    public function update(array $client): ?array
    {
        // Check Staff Has Access
        if (!\admin_access('client.update')) {
            return ['status' => false, 'message' => LANG::$permissionDenied];
        }
        // Get Inputs
        $inputs = \do_hook('request.inputs');

        // Validate UID
        if (empty($client['uid']) || empty($inputs['uid']) || ($client['uid'] !== $inputs['uid'])) {
            return ['status' => false, 'message' => LANG::$invalidUid];
        }

        // Get Statuses & Countries List
        $statuses = \do_hook('client.status.list');
        $countries = \do_hook('country.list');

        // Validate & Update Data
        $rules = [
            'fname' => 'required|min:3|max:50',
            'lname' => 'required|min:3|max:50',
            'email' => 'required|email',
            'status' => 'required|in:' . \implode(',', \array_keys($statuses)),
            'address_1' => 'required|min:1|max:255',
            'country' => 'required|in:' . \implode(',', \array_keys($countries)),
        ];
        $rule_messages = [
            'fname.required' => LANG::$requiredField,
            'fname.min' => \sprintf(LANG::$minLength, 3),
            'fname.max' => \sprintf(LANG::$maxLength, 50),
            'lname.required' => LANG::$requiredField,
            'lname.min' => \sprintf(LANG::$minLength, 3),
            'lname.max' => sprintf(LANG::$maxLength, 50),
            'email.required' => LANG::$requiredField,
            'email.email' => LANG::$invalidEmail,
            'status.required' => LANG::$requiredField,
            'status.in' => LANG::$invalidOption,
            'address_1.required' => LANG::$requiredField,
            'address_1.min' => \sprintf(LANG::$minLength, 1),
            'address_1.max' => \sprintf(LANG::$maxLength, 255),
        ];
        $this->request->validate($rules, $rule_messages);

        // Set Form Errors
        FormError::addBulk($this->request->errors());

        // Return if Form Has Error
        if (FormError::exists()) {
            return null;
        }

        // Get Client Input
        $clientInput = [
            'fname' => $inputs['fname'],
            'lname' => $inputs['lname'],
            'email' => $inputs['email'],
            'status' => $inputs['status'],
            'country' => $inputs['country']
        ];

        // Existing Client Info
        $existingClientInfo = [
            'fname' => $client['fname'],
            'lname' => $client['lname'],
            'email' => $client['email'],
            'status' => $client['status']['entity'],
            'country' => $client['address']['country']
        ];

        // Get Address Input
        $addressInput = [
            'address_1' => $inputs['address_1'],
            'address_2' => $inputs['address_2'] ?? NULL,
            'state' => $inputs['state'] ?? NULL,
            'city' => $inputs['city'] ?? NULL,
            'zip' => $inputs['zip'] ?? NULL,
            'country' => $inputs['country']
        ];

        // Existing Address Info
        $existingAddress = $client['address'];

        // Check Has Changes
        $logObj = new ChangeLog();
        $client_change_log = $logObj->check($existingClientInfo, $clientInput);
        $address_change_log = $logObj->check($existingAddress, $addressInput);

        // Update Client Info & Address if Log Not Empty
        $logs = array_merge($client_change_log, $address_change_log);

        // Take Action if Has Change Logs
        if (!empty($logs)) {
            $staff = \staff();
            // Make New Log Data
            $activity = [
                'relid' => $staff['id'],
                'task'  => LANG::$updatedClient,
                'activity' => \sprintf(
                        LANG::$clientUpdatedByStaff,
                        \do_hook('log.url', $client['username'], 'staff.client', ['client' => $client['uid']]),
                        \do_hook('log.url', $staff['username'], 'staff.staff', ['staff' => $staff['uid']]),
                    ),
                'changes' => \serialize($logs),
                'created' => \do_hook('date.format')
            ];

            try {
                // Add Updated Column
                $clientInput['updated'] = $addressInput['updated'] = \do_hook('date.format');

                // Update Client
                $this->model->transaction(function ($m) use($client, $clientInput) {
                    // Update Client
                    $m->where(['uid' => $client['uid']])->update($clientInput);
                });
                
                // Set Address Where
                $addressWhere = ['type' => 'client', 'relid' => $client['id'], 'profile_default' => 'yes'];

                // Update Address
                (new Address())->transaction(function ($m) use($addressWhere, $addressInput) {
                    $m->where($addressWhere)->update($addressInput);
                });

                // Update Log
                (new StaffActivity())->transaction(function ($m) use($activity) {
                    $m->insert($activity);
                });

                return ['status' => true, 'message' => LANG::$clientUpdatedSuccessfully];
            } catch (\Throwable $th) {
                // Set Failed Message
                $message = \do_hook('redirect.message', LANG::$createActivityFailed, $th->getMessage());
                return ['status' => true, 'message' => $message];
            }
        }
        return ['status' => false, 'message' => LANG::$noChanges];
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

        // Validate UID
        if (empty($client['uid']) || empty($inputs['uid']) ||  ($client['uid'] !== $inputs['uid'])) {
            return ['status' => false, 'message' => LANG::$invalidUid];
        }

        $data = ['reset_token' => bin2hex(random_bytes(32)), 'token_expire' => time()];
        try {
            $this->update(['uid' => $client['uid']], $data);
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

        // Validate UID
        if (empty($client['uid']) || empty($inputs['uid']) ||  ($client['uid'] !== $inputs['uid'])) {
            return ['status' => false, 'message' => LANG::$invalidUid];
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

        // Validate UID
        if (empty($client['uid']) || empty($inputs['uid']) ||  ($client['uid'] !== $inputs['uid'])) {
            return ['status' => false, 'message' => LANG::$invalidUid];
        }

        // Validate & Update Data
        $this->request->validate(['note' => 'required|min:2'], ['note.min' => sprintf(LANG::$minLength, 1)]);

        // Set Form Errors
        if (!empty($this->request->errors())) {
            return ['status' => false, 'message' => \do_hook('form.error', 'note')];
        }

        // Validate New Note Data
        $rules = [
            'title' => 'required|min:1|max:255',
            'note' => 'required'
        ];
        $rules_messages = [
            'title.required' => LANG::$requiredField,
            'title.min' => sprintf(LANG::$minLength, 1),
            'title.max' => sprintf(LANG::$maxLength, 255),
            'note.required' => LANG::$requiredField
        ];
        $this->request->validate($rules, $rules_messages);
        FormError::addBulk($this->request->errors());

        // Return if Form Has Error
        if (FormError::exists()) {
            return ['status' => false, 'message' => LANG::$generalError];
        }

        // Insert Note
        try {
            $obj = new ClientNote();
            $data = [
                'uid' => $obj->uid(),
                'relid' => $client['id'],
                'staff' => \staff()['id'],
                'title' => $inputs['title'],
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
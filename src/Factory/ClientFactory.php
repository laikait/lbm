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
use Laika\Core\Helper\Date;
use Laika\Core\Regex\Regex;
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
        $result = \do_hook('client.single', $entity);

        // Redirect to Clients if No Client Found
        if (empty($result)) {
            $this->redirect->with(LANG::$noClientFound, false)->to('staff.clients');
        }
        return $result;
    }

    /**
     * Get Limit Clients
     */
    public function limit(): array
    {
        $result = \do_hook('client.limit');

        // Check 'clients' Keys Exists
        if (!isset($result['clients'])) {
            throw new \ArgumentCountError("Not Returned [clients] Key in client.get Hook");
        }
        // Check 'total' Keys Exists
        if (!isset($result['total'])) {
            throw new \ArgumentCountError("Not Returned [total] Key in client.get Hook");
        }

        // Set Message if Empty
        if (empty($result['clients'])) {
            \do_hook('message.set', LANG::$noClientsFound, false);
        }
        return $result;
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
        $countries = call_user_func([new Country(), 'list']);

        // Validate & Update Data
        $this->request->validate([
            'fname' => 'required|min:1|max:50',
            'lname' => 'required|min:1|max:50',
            'username' => 'required|min:6|max:50|regex:/^[a-zA-Z0-9]+$/i',
            'password' => 'required',
            'cpassword' => 'required|match:password',
            'email' => 'required|email',
            'address_1' => 'required|min:1|max:255',
            'country' => 'required|in:' . implode(',', array_keys($countries)),
        ], [
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
        ]);

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
        if (FormError::hasError()) {
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
                    'uuid' => $this->model->uuid(),
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

    // /**
    //  * Get Total Client
    //  * @return int
    //  */
    // public function total(): int
    // {
    //     return $this->total ?? 0;
    // }

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
            'lname.required' => LANG::$requiredField,
            'lname.min' => sprintf(LANG::$minLength, 3),
            'lname.max' => sprintf(LANG::$maxLength, 50),
            'email.required' => LANG::$requiredField,
            'email.email' => LANG::$invalidEmail,
            'status.required' => LANG::$requiredField,
            'status.in' => LANG::$invalidOption,
            'address_1.required' => LANG::$requiredField,
            'address_1.min' => sprintf(LANG::$minLength, 1),
            'address_1.max' => sprintf(LANG::$maxLength, 255),
        ]);

        // Set Form Errors
        FormError::addBulk($this->request->errors());

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
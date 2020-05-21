<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Validation\Validator;

class Subscriber extends Controller {
	public function getUserData($users) {
		foreach($users as $user) {
			$attributes = DB::table('user_user_attribute')
				-> leftJoin('user_attribute', 'user_user_attribute.attributeid', '=', 'user_attribute.id')
				-> where('user_user_attribute.userid', $user -> id)
				-> get();

			foreach($attributes as $attribute) {
				$user -> attributes = array(
					'id' => $attribute -> id,
					'name' => $attribute -> name,
					'value' => $attribute -> value
				)
			}
		}

		return $users;
	}

	public function read($id) {
		$users = DB::table('user_user')
			-> where('id', $id)
			-> orWhere('email', $id)
			-> get();

		return response() -> json($users);
	}

	public function create() {
		$errors = array();
		$fields = array(
			'email' => 'max:255|email:rfc,dns',
			'confirmed' => 'boolean',
			'blacklisted' => 'boolean',
			'optedin' => 'boolean',
			'htmlemail' => 'boolean',
			'disabled' => 'boolean'
		);

		$email = $request -> input('email');

		$records = DB::table('user_user')
			-> where('email', $id)
			-> get();

		if (count($records) > 0) {
			return response() -> json($records);
		}

		if (!$email) {
			$errors[] = 'The subscriber email address is required.';
		}

		$all = $request -> all();

		$validator = Validator::make($all, $fields);

		if ($validator -> fails()) {
			$errors += $validator -> errors();
		}

		$confirmed = $request -> input('confirmed') ?? true;
		$blacklisted = $request -> input('blacklisted') ?? false;
		$optedin = $request -> input('optedin') ?? false;
		$htmlemail = $request -> input('htmlemail') ?? true;
		$disabled = $request -> input('disabled') ?? false;

		foreach($fields as $field) {
			unset($all[$field]);
		}

		$extraFields = array();

		foreach($all as $field => $value) {
			$extraFields[$field] = 'max:255|string';
		}

		$validator = Validator::make($all, $extraFields);

		if ($validator -> fails()) {
			$errors += $validator -> errors();
		}

		if (count($errors) > 0) {
			return response() -> json(array(
				'message' => 'Could not create a new subscriber.',
				'errors' => $errors
			), 400);
		}

		$id = DB::table('user_user')
			-> insertGetId(array(
				'email' => $email,
				'confirmed' => $confirmed,
				'blacklisted' => $blacklisted,
				'optedin' => $optedin,
				'htmlemail' => $htmlemail,
				'disabled' => $disabled
			));

		foreach($all as $field => $value) {
			$attribute = DB::table('user_attribute')
				-> where('name', $field)
				-> first();

			if (!$attribute) {
				$attributeId = DB::table('user_attribute')
					-> insertGetId(array(
						'name' => $field,
						'type' => 'textline',
						'listorder' => 0,
						'default_value' => '',
						'required' => 0,
						'tablename' => $field
					));
			} else {
				$attributeId = $attribute -> id;
			}

			DB::table('user_user_attribute')
				-> insert(array(
					'attributeid' => $attributeId,
					'userid' => $id,
					'value' => $value
				));
		}

		$users = DB::table('user_user')
			-> where('id', $id)
			-> get();

		$records = $this -> getUserData($users);

		if (count($records) > 0) {
			return response() -> json($records);
		}
	}

	public function update
}

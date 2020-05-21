<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware {
    public function handle($request, Closure $next) {
		$response = $next($request);

		$header = $request -> header('Authorization');

		$pieces = explode(' ', $header);

		$method = $pieces[0];

		$token = $pieces[1];

		switch($method) {
			case 'hmac':
				$decoded = base64_decode($header);

				$pieces = explode(':', $decoded);

				$nonce = $pieces[0];

				$digest = $pieces[1];

				foreach(DB::table('admin') -> where('superuser', 1) -> get() as $admin) {
					$key = password_hash(sprintf(
						"%s.%s",
						$admin -> loginname,
						$admin -> password
					));

					$test = hash_hmac(
						"sha512",
						$key,
						sprintf(
							"%s+%s+%s+%s",
							$request -> method(),
							$request -> route() -> getPath()
							(new Carbon()) -> format('Y-m-d h:i'),
							$nonce
						)
					);

					if ($test == $digest) {
						return $response;
					}
				}
			break;
			default:
				abort(403);
			break;
		}
    }
}

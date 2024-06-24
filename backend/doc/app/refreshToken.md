# Good to know about JWT auth:

- [JWT Access token](https://github.com/lexik/LexikJWTAuthenticationBundle/tree/3.x/Resources/doc) are e.g. short-lived, their payload may contain information about user, permissions
and plenty others. Remember by default, the payload is public. You can encrypt it (for ex using [PHP-jwt-framework](https://github.com/web-token/jwt-bundle?tab=readme-ov-file))
if you need to use Access Token to carry private information.

- JWT Refresh tokens are long-lived, their only purpose is obtaining new access tokens. Often you expose a public endpoint dedicated to refresh.
You'd better use a rate-limiter on login / refresh endpoints. In terms of workflow, you want a single-use policy (the refresh endpoint will only provide a new access token, then user will have to re-input credentials at expiry), 
and avoid send a new refresh token at refresh. Beware you need to have control on user logout, revocation, suspicious activities.

- Send tokens to frontend in http-only cookies with the Secure flag for encrypted transmission over HTTPS. This prevent setup prevents javascript being able to access content. Javascript will still be able to send
the cookies, adding "credentials:/path/to/storage/cookie" in Fetch request, but won't read / decode the tokens.

- If you need refresh tokens, read carefully [Symfony documentation](https://symfony.com/doc/current/security.html) to implement correctly the listeners, providers, authenticators and Passports.


# Here's a breakdown of the auth + refresh workflow:

## Initial Login:

### User Credentials: The user enters their login credentials (username and password) in the frontend application.
Authentication Request: The frontend sends a request to your backend API with the user's credentials.
Backend Validation: The backend validates the credentials against your user database.

### Token Issuance:
Upon successful validation, the backend generates two JWT tokens:
Access Token: This short-lived token (e.g., minutes) is used for accessing protected resources in the API. It contains user information (claims) and is signed with a secret key.
Refresh Token: This longer-lived token (e.g., days) is used to obtain new access tokens without requiring the user to re-enter their credentials. It typically contains a unique identifier and is also signed with a secret key.
Response to Frontend: The backend sends a response to the frontend containing the access token (often stored in an HttpOnly cookie) and optionally the refresh token (often stored in a separate secure HttpOnly cookie).
Accessing Protected Resources:

### API Calls: The frontend application includes the access token in the Authorization header (prefixed with "Bearer ") for subsequent API calls to access protected resources.
Token Validation: The backend API receives the request and validates the access token using a JWT library. If valid, the API grants access to the requested resource based on the user information encoded in the token's claims.

### Access Token Expiration:
Expiry Check: As the access token nears its expiration time, the frontend application checks its validity.

### Refresh Token Usage:

Refresh Request (before expiry): Before the access token expires completely, the frontend sends a request to a designated refresh token endpoint on your backend API. This request typically includes the refresh token stored in the secure HttpOnly cookie.
Backend Validation: The backend API receives the refresh request and validates the refresh token using its secret key.
New Access Token Issuance (if valid): If the refresh token is valid, the backend generates a new access token with fresh expiration.
Response to Frontend: The backend sends a response to the frontend containing the newly issued access token.
Frontend Storage: The frontend application stores the new access token securely (often in an HttpOnly cookie) and continues using it for API calls.

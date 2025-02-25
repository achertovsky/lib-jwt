# 5.0.1
## Added
- tests/TestDouble/External/JwtManagerFake.php now has exception messages

# 5.0.0
## Changed
- tests/TestDouble/External/JwtManagerFake.php gets ability to define id and expired at after init by having its properties to be public

# 4.0.0
## Added
- PSR-20
## Fixed
- Fixed LSP
## Changed
- PHP version bumped to 8.3
- Upgraded phpunit version to 11

# 3.0.1
## Fixed
- `JwtManager` can handle random string and malformed tokens

# 3.0.0
## Changed
- `JwtManager` signature constructor changed.
- `JwtManager` main and only methods is `encode` and `decode`.
- Internal structure changes
## Added
- `SignerInterface` that is used by `JwtManager` for signing the header+payload.
- `HS256Signer` as default signer

# 2.0.0
## Changed
- `HmacJwtManager` refactored, constructor signature got changed
- `HmacJwtManager` renamed to `JwtManager`, since it knows none about algorithm anymore
- `JwtManager` became main expected lib interface.
## Removed
- `JwtManagerInterface`
## Added
- Changelog
- `Token` DTO
- `JwtPartsNormalizer`: array to string and vice versa (`base64`, `json_encode` things).
- `PayloadNormalizer`: `Token::getPayload` to array and vice versa.
- `HmacTokenNormalizer`: Transforms `Token` DTO into string and vice versa. Uses `HS256` to sign.
- `TokenNormalizerInterface`. Hope, self-explanatory.
- `PayloadNormalizer::denormalize` method
- `JwtManager::decode` method that allows managers to convert jwt token string into Payload (since that is believed most valuable part of token in that lib)

# 1.0.0
- Initial lib creation

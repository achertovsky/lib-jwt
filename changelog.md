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

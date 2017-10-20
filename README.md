DadaMovies
=======================

### Installation instructions
Please run these two commands to generate certificate for token:
```
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
If you use a passphrase, do not forget to update it in your `.env`

Please also update certificate location in your `.env`

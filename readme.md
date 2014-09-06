# Crafting As A Service

An online tool to help crafters in Final Fantasy XIV: A Realm Reborn.

### Contributions
* Are welcome!
* View the CONTRIBUTING.md file for more information

### Apache Config Environment Variables

Include these inside your virtualhost definition.

```
SetEnv APP_ENVIRONMENT local|qa # Do not include for Production

SetEnv DB_HOST 127.0.0.1
SetEnv DB_NAME ffxivcrafting
SetEnv DB_PORT 9999 # Unnecessary if port is 3306
SetEnv DB_USER root
SetEnv DB_PASS password # Don't include if it's blank

SetEnv DEBUG_ENABLED 1
SetEnv ENCRYPTION_KEY 32-character-string-space-filler

SetEnv CACHE_DRIVER redis|file

# If cache driver is redis:
SetEnv REDIS_DB 0|1 # 0 is Production
SetEnv REDIS_PREFIX caas_ # I throw a qa_ before it for that environment

#SetEnv WARDROBE_LOCATION /wardrobe/qa.sqlite 
```
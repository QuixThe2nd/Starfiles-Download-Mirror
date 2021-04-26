# Starfiles Download Mirror
A simple Starfiles mirror to handle file downloads

## What this is
This script is designed to make the Starfiles network more robust. It's designed to store files forever, even if Starfiles goes down. This is a decentralized system and is made to last time. In theory, this system is able to bypass network or nation wide firewalls. If Starfiles were to go offline or be inaccessible for any reason, this system will keep the file online.

## How it works
When a file is uploaded to Starfiles, it is stored on Starfiles servers, ready to be downloaded by the user. A mirror can query Starfiles for any file it knows the id for. Once the mirror obtains a copy of this file from Starfiles, it stores it in it's cache. When Starfiles goes offline, a user is able to query a mirror for a specific file. If the mirror has a copy of this file, the user is able to download it. If a mirror does not have a copy of the file, it is able to query either Starfiles, or other known mirrors for the file.

## How is this decentralized
A mirror is able to add other mirrors to it's mirrors.json file. When a user (or another mirror) tries to download the file, if the mirror does not have a copy of it, it will query the mirrors listed in mirrors.json for the file. Mirrors are able to share their mirror list. What this means is when a hypothetical mirror 1 lists mirror 2 in it's mirrors.json file, all of mirror 1 will actually save mirror 2s mirrors.json file to call from in the future. This creates a network of mirrors that can call eachother to request files. So if Starfiles goes down, the files stay up.

## Note of Precaution
Only add trusted mirrors to your mirrors.json file. By default, only official Starfiles mirrors are in your mirrors.json. Any mirror listed in that file has the ability to send any file, without you knowing if it's the right one or not.

## Installation
1. Create directory at cache/
2. Create empty file at cache/index.php
3. Upload mirrors.json, mirror.php, and index.php
OR: copy this repo

Once the above is done, add your mirror to mirrors.json. This can be done like so:
```
{
    "mirrors": {
        "https://mirror.domain.com/":{
            "download":"mirror.php",
            "mirrors":"mirrors.json"
        },
        "https://mirror2.domain.com/":{
            "download":"",
            "mirrors":"mirrors.json"
        }
    }
}
```
### What this means:
#### Mirror 1
URL: https://mirror.domain.com
Download URL: https://mirror.domain.com/mirror.php
Mirror URL: https://mirror.domain.com/mirrors.json
#### Mirror 2
URL: https://mirror2.domain.com
Download URL: https://mirror2.domain.com
Mirror URL: https://mirror2.domain.com/mirrors.json

### Recommendations
- Have multiple domains or subdomains in your mirror.json file, even if the content is identical. This allows for a more robust network and lowers downtime.
- Don't rename mirrors.json, although the doc allows custom mirror.json files... some mirrors may not be following this rule which would make your mirror inaccessible.
### Notes
index.php is simple there to demonstrate a possible method of monetisation. If you don't want to show ads (which is recommended as it annoys users), ommit the file and rename mirror.php to index.php.

### Planned Features
- Output Authentication
- Ability to Upload Files

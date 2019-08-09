# PingFed OAuth External Consent demo
This repo is a PHP example of how to use the PingDirectory ConsentAPI to store the PF OAuth AuthZ consent (i.e. Scopes)

## Pre-requisites (these are baked into the Server Profiles here)
**PingFederate**
* PingFed 9.2+
* Reference IdP Adapter 1.5+
* OAuth AS --> Consent Page --> External

**PingDirectory**
* Sample Users
* ConsentAPI service enabled
* ConsentAPI Definition for OAuth Scopes

To start this demo, modify the environment variables in `docker-compose.yaml` to reflect where you are deploying the stack. Currently, you need to manually modify the **pingfederate** `baseURL` to match.

Then, go to http://int-docker.cpricedomain.ping-eng.com/agentlessConsent/ to start the flow.

Demo user credential:
* User.0 \ 2FederateM0re

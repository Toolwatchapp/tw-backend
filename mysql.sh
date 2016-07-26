#!/bin/bash
mysql -e < curl -u ${DATABASE_USER}:${DATABASE_PW} "${DATABASE_URL}"
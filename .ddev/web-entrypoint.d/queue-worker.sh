#!/bin/bash
#ddev-generated
# Auto-start queue worker for database queue
nohup php artisan queue:work --daemon --quiet > /dev/null 2>&1 &

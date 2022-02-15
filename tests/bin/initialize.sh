#!/bin/bash
npm run wp-env run tests-cli "wp rewrite structure '/%postname%/' --hard"
npm run wp-env run cli "wp rewrite structure '/%postname%/' --hard"

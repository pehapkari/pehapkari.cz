#!/usr/bin/env bash
sed -i -E 's/\s+$//g' packages/blog/data/_posts/*/* *.yml *.yaml *.md

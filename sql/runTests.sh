#!/bin/bash
#/********************************************************
# * This file belongs to the Badminton Ranking Project.  *
# *                                                      *
# * Copyright 2017                                       *
# *                                                      *
# * All Rights Reserved                                  *
# *                                                      *
# * Copying, distribution, usage in any form is not      *
# * allowed without  written permit.                     *
# *                                                      *
# * Philipp M. Fischer (phil.m.fischer@googlemail.com)   *
# *                                                      *
# ********************************************************/

mysql -u test < CreateBrdb1Schema.sql
mysql -u test < CreateBrdb2StoredProceduresGame.sql
mysql -u test < CreateBrdb3StoredProceduresUser.sql
mysql -u test < CreateBrdb4ResultViews.sql

mysql -u test --disable-pager --batch --raw --skip-column-names --unbuffered --database BRDB --execute 'source TestBrdb.sql'


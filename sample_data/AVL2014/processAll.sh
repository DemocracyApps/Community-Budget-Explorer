#!/bin/bash

../../gbe/artisan gb:process --instructions=extract_accounts.json data/objectcode.csv canonical/accounts.csv
../../gbe/artisan gb:process --instructions=null.json data/segment-funds.csv canonical/cat_fund.csv
../../gbe/artisan gb:process --instructions=null.json data/segment-costcenter.csv canonical/cat_costcenter.csv
../../gbe/artisan gb:process --instructions=null.json data/segment-departments.csv canonical/cat_departments.csv
../../gbe/artisan gb:process --instructions=null.json data/segment-division.csv canonical/cat_division.csv
../../gbe/artisan gb:process --instructions=null.json data/segment-function.csv canonical/cat_function.csv
../../gbe/artisan gb:process --instructions=null.json data/segment-grants.csv canonical/cat_grants.csv

#2014 Budget

../../gbe/artisan gb:process --instructions=avl_budget.json data/2014AdoptedBudget.csv canonical/2014AdoptedBudget_processed.csv

# 2010
../../gbe/artisan gb:process --instructions avl_actual.json data/general_fund_2010.csv canonical/general_fund_2010_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/civiccenter_fund_2010.csv canonical/civiccenter_fund_2010_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/masstransit_fund_2010.csv canonical/masstransit_fund_2010_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/parkingservices_fund_2010.csv canonical/parkingservices_fund_2010_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/stormwater_fund_2010.csv canonical/stormwater_fund_2010_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/streetcut_fund_2010.csv canonical/streetcut_fund_2010_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/water_fund_2010.csv canonical/water_fund_2010_processed.csv

# 2011
../../gbe/artisan gb:process --instructions avl_actual.json data/general_fund_2011.csv canonical/general_fund_2011_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/civiccenter_fund_2011.csv canonical/civiccenter_fund_2011_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/masstransit_fund_2011.csv canonical/masstransit_fund_2011_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/parkingservices_fund_2011.csv canonical/parkingservices_fund_2011_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/stormwater_fund_2011.csv canonical/stormwater_fund_2011_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/streetcut_fund_2011.csv canonical/streetcut_fund_2011_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/water_fund_2011.csv canonical/water_fund_2011_processed.csv

# 2012
../../gbe/artisan gb:process --instructions avl_actual.json data/general_fund_2012.csv canonical/general_fund_2012_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/civiccenter_fund_2012.csv canonical/civiccenter_fund_2012_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/masstransit_fund_2012.csv canonical/masstransit_fund_2012_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/parkingservices_fund_2012.csv canonical/parkingservices_fund_2012_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/stormwater_fund_2012.csv canonical/stormwater_fund_2012_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/streetcut_fund_2012.csv canonical/streetcut_fund_2012_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/water_fund_2012.csv canonical/water_fund_2012_processed.csv

# 2013
../../gbe/artisan gb:process --instructions avl_actual.json data/general_fund_2013.csv canonical/general_fund_2013_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/civiccenter_fund_2013.csv canonical/civiccenter_fund_2013_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/masstransit_fund_2013.csv canonical/masstransit_fund_2013_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/parkingservices_fund_2013.csv canonical/parkingservices_fund_2013_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/stormwater_fund_2013.csv canonical/stormwater_fund_2013_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/streetcut_fund_2013.csv canonical/streetcut_fund_2013_processed.csv
../../gbe/artisan gb:process --instructions avl_actual.json data/water_fund_2013.csv canonical/water_fund_2013_processed.csv


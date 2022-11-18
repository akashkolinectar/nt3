#
# Regular cron jobs for the _nt3_NAME_ package
#

#MAILTO=root

#
# Main heartbeat scheduler for _nt3_NAME_, launched every 5 minutes
#

*/5 * * * * root php _nt3_DATADIR_/_nt3_NAME_/webservices/cron.php --param_file=_nt3_SYSCONFDIR_/_nt3_NAME_/production/cron.params >> _nt3_LOGDIR_/_nt3_NAME_/cron.log 2>&1

# # # # #   #
# # # # #   #-- User name
# # # # #------ Day of week (0-7) 0 == 7 == Sunday
# # # #-------- Month (1 - 12)
# # #---------- Day of month (1 - 31)
# #------------ Hour (0 - 23)
#-------------- Min (0 - 59)

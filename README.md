# website-monitoring
Easy website monitoring. When your websites go down, be the first to know!
I recommend you make requests to the script from a cron job.

Pass the required parameters through a GET request like: `?token=1234&url=https://domain-to-check.com`

Don't forget to rename `.env.example` to just `.env` after updating the variables.

A third-party service is used to send emails. Go to https://sendgrid.com to get your API key if you don't have one.

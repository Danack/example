# Supervisor example

## Introduction

Supervisord is a program that runs other programs for you.

It is a great tool for controlling background tasks - things that need to be running constantly to process queues or processing other data in the background independently of your web servers.

For the vast majority of PHP projects, Supervisord will meet the needs of running background tasks. For a few projects, that have very heavy scaling needs, Supervisord might not be an appropriate choice as it lacks controls for scaling the background tasks dynamically.


## Adding programs

Add a new entry (or copy and paste one of the existing ones) in ./example/docker/supervisord/tasks.

The two settings that you should probably set are: 

* command - the actual command to be run.

* numprocs - the number of instances of that command that should be run.


## Disabling programs

Occasionally you may wish to prevent some workers running, e.g. when debugging one of the other workers.

The easiest way to disable a background worker is to move the conf file that defines that background worker from the `tasks` directory to the `tasks_disabled` directory.


## Dashboard

http://127.0.0.1:8080/

In production, this dashboard should either not be exposed, or only accessible through a tightly controlled environment. e.g. requiring people who wish to access it to remote desktop to a support machine via VPN, and have the dashboard accessible from that machine only.  

## OMG WHY ARE YOU USING SUPERVISORD AND NOT USING KUBERNETES TO DO THE SCALING??!!?

So.

Some feedback I have received when giving a talk about containers is that "I'm an idiot and that I don't understand Kubernetes and that Supervisord doesn't work with Kubernetes scaling". At least part of this is not true; I think do understand Kubernetes. 

Although Kubernetes is a great piece of technology, it is quite complex and is not always needed. 

In fact, _scaling_ isn't always needed.

For example I mostly use background processes for sending emails. The background worker for processing emails runs quite happily with just 8 MB of memory, but for safety's sake we run it with 16MB max memory. 

Google Cloud Platform will allow be to provision a server with 4GB of ram for about $25 a month. That would allow me to run about 256 background workers for sending email at the cost of $25 a month. Assuming that each background worker only sent one email at a time, and each api call to send an email took 1 second, that's:

256 emails in a second.
15,360 emails in a minute.
921,600 emails in an hour.
22,118,400 emails in a day.
663,552,000 emails in a month.

This is _slightly_ more emails than I need to send.

I would much rather pay up to $25 a month to have a system that is simple and works reliably in both dev and production, compared to having to setup a Kubernetes system with scaling which would take days of work, need upgrading when new versions of Kubernetes are released, etc. etc.

For more information about System design, please read: https://www.amazon.co.uk/Systems-Bible-Beginners-Guide-Large/dp/0961825170
 
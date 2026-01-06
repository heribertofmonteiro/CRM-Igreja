# Docker Configuration for Ministério Module

This directory contains Docker configuration files for running the ChurchCRM Ministério module with automatic queue processing and cron jobs.

## Files Overview

- **Dockerfile**: Multi-stage build with PHP 8.1, Apache, and Supervisor
- **supervisord.conf**: Process manager configuration for Apache, cron, and queue workers
- **crontab**: Scheduled tasks for message processing and maintenance
- **entrypoint.sh**: Initialization script for container setup

## Quick Start

### Build the Image

```bash
docker build -t churchcrm-ministerio:latest .
```

### Run with Docker Compose

Create a `docker-compose.yml` file:

```yaml
version: '3.8'

services:
  churchcrm:
    image: churchcrm-ministerio:latest
    ports:
      - "8080:80"
      - "8443:443"
    environment:
      - DB_HOST=db
      - DB_NAME=churchcrm
      - DB_USER=churchcrm
      - DB_PASSWORD=your_password
      - TWILIO_SID=your_twilio_sid
      - TWILIO_TOKEN=your_twilio_token
      - TWILIO_FROM=your_twilio_number
      - ZENVIA_ACCOUNT=your_zenvia_account
      - ZENVIA_PASSWORD=your_zenvia_password
      - ZENVIA_FROM=your_zenvia_from
    volumes:
      - churchcrm_data:/var/www/html
      - ./logs:/var/log/ministerio
    depends_on:
      - db
    restart: unless-stopped

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=root_password
      - MYSQL_DATABASE=churchcrm
      - MYSQL_USER=churchcrm
      - MYSQL_PASSWORD=your_password
    volumes:
      - db_data:/var/lib/mysql
    restart: unless-stopped

volumes:
  churchcrm_data:
  db_data:
```

### Start the Services

```bash
docker-compose up -d
```

## Environment Variables

### Database Configuration
- `DB_HOST`: Database host (default: localhost)
- `DB_NAME`: Database name (default: churchcrm)
- `DB_USER`: Database user
- `DB_PASSWORD`: Database password

### SMS Providers

#### Twilio
- `TWILIO_SID`: Your Twilio Account SID
- `TWILIO_TOKEN`: Your Twilio Auth Token
- `TWILIO_FROM`: Your Twilio phone number

#### Zenvia
- `ZENVIA_ACCOUNT`: Your Zenvia account identifier
- `ZENVIA_PASSWORD`: Your Zenvia password
- `ZENVIA_FROM`: Sender identifier for Zenvia

### Email Configuration
Configure through ChurchCRM admin panel or SystemConfig:
- SMTP Host, Port, User, Password
- From email address and name

## Volume Mounts

### Required Volumes
- `/var/www/html`: Application files
- `/var/log/ministerio`: Module-specific logs
- `/var/log/supervisor`: Supervisor logs

### Backup Volumes
- `/var/lib/mysql`: Database data (if using MySQL container)

## Cron Jobs

The container automatically runs these scheduled tasks:

1. **Message Queue Processing** (every 5 minutes)
   - Processes pending messages
   - Handles retries for failed messages
   - Updates message status

2. **Log Cleanup** (daily at 3 AM)
   - Removes logs older than 30 days
   - Prevents disk space issues

3. **Token Cleanup** (daily at 4 AM)
   - Removes expired RSVP tokens
   - Maintains system performance

4. **Health Check** (weekly on Sunday at 2 AM)
   - Verifies system integrity
   - Checks for failed messages
   - Monitors disk usage

## Queue Workers

The container runs multiple queue workers:

1. **Primary Worker**: Processes messages immediately
2. **Retry Worker**: Handles failed messages with delays

Workers are managed by Supervisor and automatically restart on failure.

## Monitoring

### Check Container Status
```bash
docker-compose ps
docker-compose logs churchcrm
```

### View Queue Statistics
Access the admin dashboard or use the API:
```bash
curl http://localhost:8080/api/v2/ministerio/queue/stats
```

### Monitor Logs
```bash
# Supervisor logs
docker-compose exec churchcrm tail -f /var/log/supervisor/supervisord.log

# Queue worker logs
docker-compose exec churchcrm tail -f /var/log/ministerio/queue-worker.log

# Cron logs
docker-compose exec churchcrm tail -f /var/log/ministerio/cron.log
```

## Troubleshooting

### Container Won't Start
1. Check Docker logs: `docker-compose logs churchcrm`
2. Verify database connection
3. Check file permissions

### Messages Not Sending
1. Check queue worker logs
2. Verify SMS provider configuration
3. Check message status in admin panel

### High Memory Usage
1. Reduce queue worker processes in supervisord.conf
2. Increase PHP memory limit
3. Check for message loops

### Database Connection Issues
1. Verify environment variables
2. Check MySQL container status
3. Test connection manually

## Security Considerations

### Environment Variables
- Never commit credentials to version control
- Use Docker secrets for production
- Rotate API keys regularly

### Network Security
- Use HTTPS in production
- Configure firewall rules
- Limit container network access

### File Permissions
- Container automatically sets correct permissions
- Logs are protected from web access
- Sensitive files are restricted

## Production Deployment

### SSL/TLS Configuration
Mount SSL certificates:
```yaml
volumes:
  - ./ssl/cert.pem:/etc/ssl/certs/churchcrm.crt
  - ./ssl/key.pem:/etc/ssl/private/churchcrm.key
```

### Backup Strategy
```bash
# Database backup
docker-compose exec db mysqldump -u root -p churchcrm > backup.sql

# Files backup
docker run --rm -v churchcrm_data:/data -v $(pwd):/backup alpine tar czf /backup/churchcrm-files.tar.gz /data
```

### Performance Tuning
- Adjust PHP-FPM settings
- Configure MySQL for your workload
- Monitor resource usage
- Scale queue workers as needed

## Maintenance

### Update Container
```bash
docker-compose pull
docker-compose up -d
```

### Clean Up
```bash
docker-compose down
docker volume prune
```

### Database Migration
Follow ChurchCRM upgrade procedures and backup before upgrading.
# Baltic Migration Tool

## Quick Start

### Setup

1. **Install PHP**: Ensure PHP is installed on your computer.
2. **Install Dependencies**: Run `composer install` in your project directory to install required PHP packages.
3. **Configure Settings**:
   - Copy the configuration template:
     ```shell
     cp config-example.php config.php
     ```
   - Edit `config.php` with your details:
     ```php
     <?php
     define('VOOG_API', 'your_api_key_here');
     define('VOOG_URL', 'your_voog_site_url_here');
     ```
     Replace `your_api_key_here` and `your_voog_site_url_here` with your actual Voog API key and website URL.
4. **Prepare Data File**:
   - Copy the example data file:
     ```shell
     cp data-example.json data.json
     ```
   - Open `data.json` and insert your data according to the JSON structure. Make sure the data matches the expected format for the migration process.

### Running a Migration

To migrate data for a specific JSON index, use the following command:

```shell
php migrate.php {jsonIndexId} | tee -a log
```

Replace `{jsonIndexId}` with the JSON index ID you intend to process. The command processes the migration and appends output to a `log` file.

#### Examples

- To migrate data for JSON index ID 35:

  ```shell
  php migrate.php 35 | tee -a log
  ```

- To sequentially process multiple JSON index IDs (e.g., 35, 36, 37):

  ```shell
  for id in 35 36 37; do php migrate.php $id | tee -a log; done
  ```

### What to Expect

After executing the command, migration statuses will be displayed in the terminal and logged in the `log` file. Successful migrations will indicate with messages such as "Article #XXXXXX imported successfully!"

### Troubleshooting

- **PHP Not Found**: Confirm PHP is installed by running `php -v`.
- **Dependency Errors**: Ensure `composer install` was successful.
- **Script Permissions**: Make `migrate.php` executable with `chmod +x migrate.php`.
- **Data Format Issues**: Verify that `data.json` matches the required structure for your migrations.

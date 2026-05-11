<?php
// SECURITY: Better connection file check
if (!file_exists("./Connections/wcba.php")) {
  header("location:./db-config.php?code=1378907769354882");
  exit;
}
require_once('./Connections/wcba.php');
require_once('./menu.php');

// Initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// SECURITY: Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['loggedin'])) {
  header('Location: ./login.php');
  exit;
}
if ($_SESSION['level'] < 1) {
  header('Location: ./nopermission.php');
  exit;
}

// SECURITY Helper: Quick function to escape output and prevent XSS
function e($string)
{
  return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// ====================================================================
// CHART DATA QUERIES
// ====================================================================

// 1. String Jobs per Quarter (Last 5 Years / 20 Quarters)
$query_quarters = "
    SELECT quarter_year, job_count FROM (
        SELECT 
            CONCAT('Q', QUARTER(STR_TO_DATE(collection_date, '%d/%m/%Y')), ' ', YEAR(STR_TO_DATE(collection_date, '%d/%m/%Y'))) as quarter_year, 
            YEAR(STR_TO_DATE(collection_date, '%d/%m/%Y')) as job_year,
            QUARTER(STR_TO_DATE(collection_date, '%d/%m/%Y')) as job_quarter,
            COUNT(job_id) as job_count 
        FROM stringjobs 
        WHERE collection_date != '' AND collection_date IS NOT NULL
        GROUP BY job_year, job_quarter 
        ORDER BY job_year DESC, job_quarter DESC 
        LIMIT 20
    ) AS recent_quarters
    ORDER BY job_year ASC, job_quarter ASC";
$Recordset_quarters = mysqli_query($conn, $query_quarters) or die(mysqli_error($conn));

$months_labels = [];
$months_data = [];
while ($row = mysqli_fetch_assoc($Recordset_quarters)) {
  $months_labels[] = $row['quarter_year'];
  $months_data[] = $row['job_count'];
}

// 2. Most Popular Strings (Top 10 - Filtered)
$query_strings = "
    SELECT 
        TRIM(CONCAT(IFNULL(all_string.brand, ''), ' ', IFNULL(all_string.type, ''))) as string_name, 
        COUNT(stringjobs.job_id) as job_count 
    FROM stringjobs 
    LEFT JOIN string ON stringjobs.stringid = string.stringid 
    LEFT JOIN all_string ON string.stock_id = all_string.string_id 
    GROUP BY string_name 
    HAVING string_name != '' 
       AND LOWER(string_name) NOT LIKE '%unknown%' 
       AND LOWER(string_name) NOT LIKE '%string generic%'
    ORDER BY job_count DESC 
    LIMIT 10";
$Recordset_strings = mysqli_query($conn, $query_strings) or die(mysqli_error($conn));

$strings_labels = [];
$strings_data = [];
while ($row = mysqli_fetch_assoc($Recordset_strings)) {
  $strings_labels[] = $row['string_name'];
  $strings_data[] = $row['job_count'];
}

// 3. Customer with the most string jobs (Top 10)
$query_customers = "
    SELECT 
        stringjobs.customerid,
        MAX(customer.Name) as customer_name,
        COUNT(stringjobs.job_id) as job_count 
    FROM stringjobs 
    LEFT JOIN customer ON stringjobs.customerid = customer.cust_ID 
    GROUP BY stringjobs.customerid 
    ORDER BY job_count DESC 
    LIMIT 10";
$Recordset_customers = mysqli_query($conn, $query_customers) or die(mysqli_error($conn));

$customers_labels = [];
$customers_data = [];
while ($row = mysqli_fetch_assoc($Recordset_customers)) {
  $c_name = trim($row['customer_name'] ?? '');
  $customers_labels[] = ($c_name === '') ? 'Unknown Customer' : $c_name;
  $customers_data[] = (int)$row['job_count']; // Forces strict integer format
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <script>
    const storedTheme = localStorage.getItem('themeSwitch');
    if (storedTheme) {
      document.documentElement.setAttribute('data-theme', storedTheme);
    }
  </script>
  <script type="text/javascript" src="./js/theme.js"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <title>SDBA - Statistics</title>
  <link rel="icon" type="image/png" href="./img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="./img/favicon-16x16.png" sizes="16x16" />
  <meta name="color-scheme" content="dark light" />
  <meta name="theme-color" media="(prefers-color-scheme: dark)" />
  <meta name="theme-color" media="(prefers-color-scheme: light)" />

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .chart-container {
      background: var(--bg-color, #ffffff);
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 30px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Ensure dark mode works with charts */
    [data-theme="dark"] .chart-container {
      background: #2a2a2a;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
    }
  </style>
</head>

<body data-spy="scroll" data-target="#main-nav">
  <?php echo $main_menus; ?>

  <div class="home-section diva">
    <div class="subheader"></div>
    <p class="fxdtextb"><strong>Job</strong> Statistics</p>

    <div class="container-fluid" style="margin-top: 150px;">
      <div class="row">
        <div class="col-12">
          <div class="chart-container">
            <h5 class="text-center mb-3">String Jobs Per Quarter (Last 5 Years)</h5> <canvas id="jobsPerMonthChart" height="250"></canvas>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="chart-container">
            <h5 class="text-center mb-3">Most Popular Strings</h5>
            <canvas id="popularStringsChart" height="200"></canvas>
          </div>
        </div>

        <div class="col-md-6">
          <div class="chart-container">
            <h5 class="text-center mb-3">Top Customers (By Volume)</h5>
            <canvas id="topCustomersChart" height="200"></canvas>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <script>
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    if (hamburger) {
      hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("active");
      });
    }

    var themeSwitch = document.getElementById('themeSwitch');

    // Function to get grid line color based on theme
    function getGridColor() {
      return (localStorage.getItem('themeSwitch') === 'dark') ? 'rgba(255, 255, 255, 0.2)' : 'rgba(0, 0, 0, 0.1)';
    }

    // Function to get text color based on theme
    function getTextColor() {
      return (localStorage.getItem('themeSwitch') === 'dark') ? '#ffffff' : '#333333'; // Pure white in dark mode
    }
    // Chart Options Template to handle dark/light mode text
    const commonOptions = {
      responsive: true,
      plugins: {
        legend: {
          labels: {
            color: getTextColor()
          }
        }
      },
      scales: {
        x: {
          ticks: {
            color: getTextColor()
          },
          grid: {
            color: getGridColor()
          }
        },
        y: {
          ticks: {
            color: getTextColor()
          },
          grid: {
            color: getGridColor()
          }
        }
      }
    };

    // 1. String Jobs Per Month (Line Chart)
    const ctxMonths = document.getElementById('jobsPerMonthChart').getContext('2d');
    const jobsPerMonthChart = new Chart(ctxMonths, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($months_labels); ?>,
        datasets: [{
          label: 'Total Restrings',
          data: <?php echo json_encode($months_data); ?>,
          borderColor: 'rgba(54, 162, 235, 1)',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderWidth: 2,
          tension: 0.3,
          fill: true
        }]
      },
      options: commonOptions
    });

    // 2. Most Popular Strings (Doughnut Chart)
    const ctxStrings = document.getElementById('popularStringsChart').getContext('2d');
    const popularStringsChart = new Chart(ctxStrings, {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode($strings_labels); ?>,
        datasets: [{
          data: <?php echo json_encode($strings_data); ?>,
          backgroundColor: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)',
            'rgba(83, 102, 255, 0.7)',
            'rgba(40, 159, 64, 0.7)',
            'rgba(210, 99, 132, 0.7)',
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              color: getTextColor()
            }
          }
        }
      }
    });

    // 3. Top Customers (Bar Chart)
    const ctxCustomers = document.getElementById('topCustomersChart').getContext('2d');

    // Destroy existing chart if it exists to prevent overlap issues
    if (window.topCustomersChart instanceof Chart) {
      window.topCustomersChart.destroy();
    }

    window.topCustomersChart = new Chart(ctxCustomers, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($customers_labels); ?>,
        datasets: [{
          label: 'Jobs Completed',
          data: <?php echo json_encode($customers_data); ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.7)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        indexAxis: 'y', // This forces the chart sideways so names fit perfectly!
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: getTextColor()
            }
          }
        },
        scales: {
          x: {
            ticks: {
              color: getTextColor(),
              stepSize: 1
            }, // Stops decimal numbers
            grid: {
              color: getGridColor()
            }
          },
          y: {
            ticks: {
              color: getTextColor()
            },
            grid: {
              color: getGridColor()
            }
          }
        }
      }
    });

    // Handle Theme Switch Logic for Charts
    if (themeSwitch) {
      var darkThemeSelected = (localStorage.getItem('themeSwitch') === 'dark');
      themeSwitch.checked = darkThemeSelected;

      themeSwitch.addEventListener('change', function(event) {
        if (themeSwitch.checked) {
          document.body.setAttribute('data-theme', 'dark');
          document.getElementById("imglogo").src = "./img/logo-dark.png";
          localStorage.setItem('themeSwitch', 'dark');
        } else {
          document.body.removeAttribute('data-theme');
          document.getElementById("imglogo").src = "./img/logo.png";
          localStorage.removeItem('themeSwitch');
        }
        // Force charts to update their colors
        [jobsPerMonthChart, popularStringsChart, topCustomersChart].forEach(chart => {
          const txtColor = getTextColor();
          const grColor = getGridColor();
          chart.options.plugins.legend.labels.color = txtColor;
          if (chart.options.scales) {
            chart.options.scales.x.ticks.color = txtColor;
            chart.options.scales.x.grid.color = grColor;
            chart.options.scales.y.ticks.color = txtColor;
            chart.options.scales.y.grid.color = grColor;
          }
          chart.update();
        });
      });
    }

    var imgsrc = localStorage.getItem('themeSwitch');
    var logo = document.getElementById("imglogo");
    if (logo) {
      logo.src = (imgsrc === "dark") ? "./img/logo-dark.png" : "./img/logo.png";
    }
  </script>
</body>

</html>
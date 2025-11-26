<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Fetch latest fines
$latestFines = getLatestFines($conn, 10);

// Fetch analytics data
$regulatorStats = getRegulatorStats($conn);
$sectorStats = getSectorStats($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Protection & Privacy Fines - Track Global Privacy & Data Protection Fines</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Track Global Privacy & Data Protection Fines</h1>
                    <p>Stay updated on enforcement actions and fines from regulators around the world. Monitor compliance trends and protect your organisation.</p>
                    <div class="hero-buttons">
                        <a href="database.php" class="btn btn-primary">
                            Explore Fines <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="alerts.php" class="btn btn-secondary">
                            Sign Up for Alerts
                        </a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="stats-box">
                        <div class="stat-number">12,500+</div>
                        <div class="stat-label">Enforcement Actions Tracked</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Filter Section -->
    <section class="search-section">
        <div class="container">
            <form action="database.php" method="GET" class="search-form">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search by organisation, sector, date, GDPR article, or regulation...">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>

                <div class="filters">
                    <select name="regulator">
                        <option value="">All Regulators</option>
                        <option value="ICO (UK)">ICO (UK)</option>
                        <option value="CNIL (France)">CNIL (France)</option>
                        <option value="DPC (Ireland)">DPC (Ireland)</option>
                        <option value="BfDI (Germany)">BfDI (Germany)</option>
                        <option value="AEPD (Spain)">AEPD (Spain)</option>
                        <option value="FTC (USA)">FTC (USA)</option>
                        <option value="OAIC (Australia)">OAIC (Australia)</option>
                        <option value="OPC (Canada)">OPC (Canada)</option>
                    </select>

                    <select name="sector">
                        <option value="">All Sectors</option>
                        <option value="Finance & Banking">Finance & Banking</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Technology">Technology</option>
                        <option value="Retail & E-commerce">Retail & E-commerce</option>
                        <option value="Telecommunications">Telecommunications</option>
                        <option value="Public Sector">Public Sector</option>
                    </select>

                    <select name="year">
                        <option value="">All Years</option>
                        <?php for($y = date('Y'); $y >= 2018; $y--): ?>
                            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>

                    <select name="violation_type">
                        <option value="">All Violation Types</option>
                        <option value="Security Breach">Security Breach</option>
                        <option value="Inadequate Security">Inadequate Security</option>
                        <option value="Consent Issues">Consent Issues</option>
                        <option value="Transparency">Transparency</option>
                        <option value="Data Transfer">Data Transfer</option>
                        <option value="Children's Privacy">Children's Privacy</option>
                    </select>
                </div>
            </form>
        </div>
    </section>

    <!-- Latest Fines Section -->
    <section class="latest-fines">
        <div class="container">
            <div class="section-header">
                <h2>Latest Enforcement Actions</h2>
                <a href="database.php" class="view-all">
                    View All <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <div class="fines-list">
                <?php if (empty($latestFines)): ?>
                    <p class="no-data">No fines found. Add data to your database to see results.</p>
                <?php else: ?>
                    <?php foreach ($latestFines as $fine): ?>
                        <div class="fine-card">
                            <div class="fine-org">
                                <h3><?php echo htmlspecialchars($fine['organisation']); ?></h3>
                                <p><?php echo htmlspecialchars($fine['regulator']); ?></p>
                            </div>
                            <div class="fine-amount">
                                <div class="amount"><?php echo formatAmount($fine['fine_amount'], $fine['currency']); ?></div>
                                <div class="date"><?php echo date('M Y', strtotime($fine['fine_date'])); ?></div>
                            </div>
                            <div class="fine-type">
                                <span class="badge badge-red"><?php echo htmlspecialchars($fine['violation_type']); ?></span>
                                <div class="law"><?php echo htmlspecialchars($fine['law'] . ' ' . $fine['articles_breached']); ?></div>
                            </div>
                            <div class="fine-summary">
                                <p><?php echo htmlspecialchars(substr($fine['summary'], 0, 100)); ?>...</p>
                            </div>
                            <div class="fine-action">
                                <a href="case.php?id=<?php echo $fine['id']; ?>" class="btn btn-view">View Case</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Analytics Preview -->
    <section class="analytics">
        <div class="container">
            <div class="section-header centered">
                <h2>Analytics & Insights</h2>
                <p>Understand global enforcement trends at a glance</p>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <h3><i class="fas fa-chart-bar"></i> Top Regulators by Enforcement Actions</h3>
                    <div class="chart-bars">
                        <?php foreach ($regulatorStats as $stat): ?>
                            <div class="bar-item">
                                <div class="bar-label">
                                    <span><?php echo htmlspecialchars($stat['regulator']); ?></span>
                                    <span><?php echo $stat['count']; ?> cases</span>
                                </div>
                                <div class="bar-bg">
                                    <div class="bar-fill" style="width: <?php echo ($stat['count'] / $regulatorStats[0]['count'] * 100); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="chart-card">
                    <h3><i class="fas fa-chart-line"></i> Most Fined Sectors</h3>
                    <div class="chart-bars">
                        <?php foreach ($sectorStats as $stat): ?>
                            <div class="bar-item">
                                <div class="bar-label">
                                    <span><?php echo htmlspecialchars($stat['sector']); ?></span>
                                    <span><?php echo $stat['count']; ?> cases</span>
                                </div>
                                <div class="bar-bg">
                                    <div class="bar-fill bar-fill-purple" style="width: <?php echo ($stat['count'] / $sectorStats[0]['count'] * 100); ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="dashboards.php" class="btn btn-primary btn-large">View Full Dashboards</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="section-header centered">
                <h2>How It Works</h2>
                <p>Powerful tools for compliance professionals</p>
            </div>

            <div class="features-list">
                <div class="feature-item">
                    <div class="feature-icon blue">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Search & Filter</h3>
                        <p>Advanced search across 12,50 0+ enforcement actions with powerful filtering by regulator, sector, date, and violation type</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon indigo">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Dashboards & Reports</h3>
                        <p>Visualize trends, compare regulators, and generate custom reports for stakeholder presentations</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon green">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Alerts & Notifications</h3>
                        <p>Receive instant alerts on new enforcement actions relevant to your industry or jurisdiction</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon purple">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h3>Case Summaries</h3>
                        <p>Detailed summaries with key facts, legal basis, and outcomes to understand enforcement reasoning</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Stay Ahead of Global Enforcement</h2>
            <p>Join thousands of compliance professionals who trust GlobalFines to stay informed. Sign up for alerts and full database access today.</p>
            <div class="cta-buttons">
                <a href="database.php" class="btn btn-outline">Explore Database</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>

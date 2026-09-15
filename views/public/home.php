<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QueueLess - Virtual Queue & Appointment Management</title>
    <link rel="stylesheet" href="assets/css/style.css?v=9">
</head>
<body class="public-body">
<header class="public-header">
    <div class="container public-nav">
        <a class="brand dark" href="index.php">
            <span class="brand-mark">Q</span>
            <span>QueueLess</span>
        </a>
        <nav>
            <a href="#live-queue">Live queue</a>
            <a href="index.php?action=login">Sign in</a>
            <a class="button small" href="index.php?action=register">Create account</a>
        </nav>
    </div>
</header>

<section class="hero" id="live-queue">
    <div class="container hero-grid">
        <div class="hero-copy">
            <span class="eyebrow">Virtual queue, real-time updates</span>
            <h1>Skip the line. Keep your place.</h1>
            <p>Join a queue from anywhere, follow your token in real time, or book an appointment before you arrive.</p>

            <div class="hero-actions">
                <a class="button" href="index.php?action=register">Create customer account</a>
                <a class="button outline" href="index.php?action=login">Sign in</a>
            </div>

            <div class="hero-points">
                <div class="point-card">
                    <span class="point-icon">01</span>
                    <div><b>Join a queue</b><small>Choose a service and receive your digital token.</small></div>
                </div>
                <div class="point-card">
                    <span class="point-icon">02</span>
                    <div><b>Track your turn</b><small>See who is being served and how many people are ahead.</small></div>
                </div>
                <div class="point-card accent">
                    <span class="point-icon">03</span>
                    <div><b>Book ahead</b><small>Reserve a time slot and check in when your appointment is due.</small></div>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="visual-card main-card">
                <div class="visual-top">
                    <span class="status-pill open">Live Queue</span>
                    <small id="publicServiceName">Loading service...</small>
                </div>

                <div class="token-screen">
                    <span>Now serving</span>
                    <b id="publicCurrentToken">-</b>
                    <small><strong id="publicWaiting">0</strong> waiting now</small>
                </div>

                <div class="visual-mini-grid">
                    <div>
                        <span>Service</span>
                        <b id="publicServiceShort">-</b>
                    </div>
                    <div>
                        <span>Counter</span>
                        <b id="publicCounter">-</b>
                    </div>
                </div>
            </div>

            <div class="visual-card side-card">
                <div class="preview-head">Available services</div>
                <div class="service-pill-row" id="publicServiceButtons">
                    <span class="muted">Loading services...</span>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="public-footer">
    <div class="container">
        <span>QueueLess &copy; <?php echo date('Y'); ?></span>
        <span>Virtual Queue &amp; Appointment Management</span>
    </div>
</footer>
<script src="assets/js/public.js"></script>
</body>
</html>

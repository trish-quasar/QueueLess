<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>QueueLess</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="public-header">
    <div class="container topbar-inner">
        <a class="brand dark" href="index.php">QueueLess</a>
        <nav>
            <a href="index.php?action=login">Login</a>
            <a class="button small" href="index.php?action=register">Register</a>
        </nav>
    </div>
</header>

<section class="hero hero-rich">
    <div class="container hero-grid hero-grid-enhanced">
        <div class="hero-copy">
            <span class="eyebrow">SMARTER SERVICE WAITING</span>
            <h1>Skip the physical line. Keep your place.</h1>
            <p>Join a virtual queue, follow your token in real time, or reserve an appointment before you arrive.</p>
            <div class="hero-actions">
                <a class="button" href="index.php?action=register">Create customer account</a>
                <a class="button outline" href="index.php?action=login">Sign in</a>
            </div>

            <div class="hero-points">
                <div class="point-card">
                    <span class="point-icon">●</span>
                    <div>
                        <b>Take a token online</b>
                        <small>Join your service queue without standing in line.</small>
                    </div>
                </div>
                <div class="point-card">
                    <span class="point-icon">●</span>
                    <div>
                        <b>Track your turn</b>
                        <small>See the current token, queue status and counter updates.</small>
                    </div>
                </div>
                <div class="point-card accent">
                    <span class="point-icon">●</span>
                    <div>
                        <b>Book an appointment</b>
                        <small>Reserve a time slot in advance and manage it later.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="visual-card main-card">
                <div class="visual-top">
                    <span class="status-pill open">Live Queue</span>
                    <small id="publicServiceName">Loading...</small>
                </div>

                <div class="token-screen">
                    <span>Now serving</span>
                    <b id="publicCurrentToken">-</b>
                    <small><span id="publicWaiting">0</span> people waiting</small>
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
                <div class="preview-head">Available Services</div>
                <div class="service-pill-row" id="publicServiceButtons">
                    <span class="muted">Loading services...</span>
                </div>
            </div>

            <div class="visual-float float-one">Live updates</div>
            <div class="visual-float float-two">Quick check-in</div>
        </div>
    </div>
</section>

<footer>
    <div class="container">QueueLess &copy; <?php echo date('Y'); ?></div>
</footer>
<script src="assets/js/public.js"></script>
</body>
</html>

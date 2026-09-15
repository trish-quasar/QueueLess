<?php
$pageTitle = 'Appointments';
require 'views/includes/header.php';
?>
<div class="page-head">
    <div>
        <h1>
            Appointments
        </h1>
        <p>
            Reserve a future time and check in on the appointment date.
        </p>
    </div>
</div>
<div class="two-col">
    <section class="card">
        <h2>
            Book appointment
        </h2>
        <form method="POST" action="index.php?action=book_appointment" id="appointmentForm">
            <label>
                Service
            </label>
            <select name="service_id" id="service_id">
                <option value="">
                    Select service
                </option>
                <?php while ($s = $services->fetch_assoc()): ?>
                    <option value="<?php echo $s['service_id']; ?>">
                        <?php echo htmlspecialchars($s['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label>
                Date
            </label>
            <input type="date" name="appointment_date" id="appointment_date" min="<?php echo date('Y-m-d'); ?>">
            <label>
                Time
            </label>
            <select name="slot_time" id="slot_time">
                <option value="">
                    Select time
                </option>
                <option value="09:00:00">
                    9:00 AM
                </option>
                <option value="10:00:00">
                    10:00 AM
                </option>
                <option value="11:00:00">
                    11:00 AM
                </option>
                <option value="14:00:00">
                    2:00 PM
                </option>
                <option value="15:00:00">
                    3:00 PM
                </option>
            </select>
            <button class="button">
                Book appointment
            </button>
        </form>
    </section>
    <section class="card">
        <h2>
            Your appointments
        </h2>
        <div class="list">
            <?php while ($a = $appointments->fetch_assoc()): ?>
                <div class="appointment-row">
                    <div>
                        <b>
                            <?php echo htmlspecialchars($a['service_name']); ?>
                        </b>
                        <small>
                            <?php echo htmlspecialchars($a['appointment_date']); ?>
                            at
                            <?php echo date('g:i A',strtotime($a['slot_time'])); ?>
                        </small>
                        <span class="status-pill neutral">
                            <?php echo htmlspecialchars($a['status']); ?>
                        </span>
                    </div>
                    <div class="inline-actions">
                        <?php if ($a['status']==='Booked'): ?>
                            <form method="POST" action="index.php?action=cancel_appointment">
                                <input type="hidden" name="appointment_id" value="<?php echo $a['appointment_id']; ?>">
                                <button class="text-button danger-text">
                                    Cancel
                                </button>
                            </form>
                            <?php
                            $appointment_time = strtotime($a['appointment_date'] . ' ' . $a['slot_time']);
                            $can_check_in = time() >= ($appointment_time - 30 * 60)
                                && time() <= ($appointment_time + 60 * 60);
                            ?>
                            <?php if ($can_check_in): ?>
                                <form method="POST" action="index.php?action=check_in">
                                    <input type="hidden" name="appointment_id" value="<?php echo $a['appointment_id']; ?>">
                                    <button class="button small">
                                        Check in
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</div>
<?php require 'views/includes/footer.php'; ?>

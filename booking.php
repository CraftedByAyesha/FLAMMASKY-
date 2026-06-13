<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FLAMMA SKY – Book a Table</title>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    .hero { background: #0f0500; padding: 4.5rem 2rem; text-align: center; border-bottom: 2px solid #c8732a; }
    .hero h1 { color: #fff; font-size: 38px; letter-spacing: 3px; margin-top: 10px; }

    .main { display: flex; gap: 2.5rem; max-width: 980px; margin: 3rem auto; padding: 0 2rem; flex-wrap: wrap; align-items: flex-start; }

    .form-card { background: #fff; border: 1px solid #ead5bb; border-radius: 4px; padding: 2.5rem; flex: 1; min-width: 300px; }
    .form-card h2 { font-size: 11px; color: #c8732a; letter-spacing: 3px; margin-bottom: 2rem; padding-bottom: 10px; border-bottom: 1px solid #ead5bb; font-family: 'Segoe UI', sans-serif; }

    .field { margin-bottom: 1.1rem; }
    .field label { display: block; font-size: 11px; color: #888; margin-bottom: 6px; letter-spacing: 1px; font-family: 'Segoe UI', sans-serif; }
    .field input, .field select, .field textarea {
      width: 100%; padding: 11px 14px; border: 1px solid #ddd; border-radius: 3px;
      font-size: 14px; color: #1a0a00; background: #fdf8f2; outline: none;
      font-family: 'Segoe UI', sans-serif; transition: border-color 0.2s;
    }
    .field input:focus, .field select:focus, .field textarea:focus {
      border-color: #c8732a; box-shadow: 0 0 0 3px rgba(200,115,42,0.1);
    }
    .field textarea { min-height: 90px; resize: vertical; }
    .r2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .sbtn {
      background: #c8732a; color: #fff; border: none;
      padding: 14px; width: 100%; font-size: 13px; letter-spacing: 2px;
      cursor: pointer; border-radius: 3px; margin-top: 10px;
      font-family: 'Segoe UI', sans-serif; transition: background 0.2s;
    }
    .sbtn:hover { background: #a85e20; }

    .success-msg {
      display: none; background: #e7f5e7; border: 1px solid #81c784;
      color: #2e7d32; padding: 14px; border-radius: 3px; font-size: 13px;
      margin-top: 1rem; text-align: center; line-height: 1.7;
      font-family: 'Segoe UI', sans-serif;
    }
    .error-msg {
      display: none; background: #fdecea; border: 1px solid #f09595;
      color: #b71c1c; padding: 14px; border-radius: 3px; font-size: 13px;
      margin-top: 1rem; font-family: 'Segoe UI', sans-serif;
    }

    .info-card { width: 280px; flex-shrink: 0; }
    .info-block { background: #fff; border: 1px solid #ead5bb; border-radius: 4px; padding: 1.75rem; margin-bottom: 16px; }
    .info-block h3 { font-size: 11px; color: #c8732a; letter-spacing: 2px; margin-bottom: 1.2rem; padding-bottom: 8px; border-bottom: 1px solid #ead5bb; font-family: 'Segoe UI', sans-serif; }
    .c-row { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .c-row:last-child { margin-bottom: 0; }
    .c-icon { width: 38px; height: 38px; border-radius: 50%; background: #fdf0e4; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #ead5bb; }
    .c-icon svg { width: 16px; height: 16px; stroke: #c8732a; fill: none; stroke-width: 1.8; }
    .c-label { font-size: 10px; color: #aaa; letter-spacing: 1px; font-family: 'Segoe UI', sans-serif; margin-bottom: 3px; }
    .c-val { font-size: 13px; color: #1a0a00; font-family: 'Segoe UI', sans-serif; }
    .hours-item { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #f5ece0; font-family: 'Segoe UI', sans-serif; font-size: 13px; }
    .hours-item:last-child { border-bottom: none; }
    .hours-item .day { color: #888; font-size: 12px; }
    .hours-item .time { color: #1a0a00; font-weight: 600; }
    .notes-box { background: #fdf8f2; border-radius: 3px; padding: 12px; font-size: 12px; color: #888; line-height: 1.8; font-family: 'Segoe UI', sans-serif; }

    @media(max-width: 700px) {
      .r2 { grid-template-columns: 1fr; }
      .info-card { width: 100%; }
    }
  </style>
</head>
<body>

<nav>
  <div class="logo">FLAMMA SKY<span>BBQ &amp; GRILL</span></div>
  <div class="nav-links">
    <a href="index.html">Home</a>
    <a href="about.html">About</a>
    <a href="menu.html">Menu</a>
    <a href="booking.php" class="active">Book a Table</a>
  </div>
</nav>

<section class="hero">
  <div class="kicker">WE AWAIT YOU</div>
  <h1>Reserve Your Table</h1>
</section>

<div class="contact-band">
  <div class="cb-item"><div class="label">PHONE</div><div class="val">+91 9642410067</div></div>
  <div class="cb-item"><div class="label">EMAIL</div><div class="val">flammaskyva@gmail.com</div></div>
  <div class="cb-item"><div class="label">HOURS</div><div class="val">12 PM – 11 PM, All Days</div></div>
</div>

<div class="main">
  <div class="form-card">
    <h2>BOOKING DETAILS</h2>
    <form id="bookingForm" action="booking_submit.php" method="POST">
      <div class="r2">
        <div class="field"><label>FULL NAME *</label><input type="text" name="name" placeholder="Your name" required/></div>
        <div class="field"><label>PHONE NUMBER *</label><input type="tel" name="phone" placeholder="+91 XXXXX XXXXX" required/></div>
      </div>
      <div class="field"><label>EMAIL ADDRESS</label><input type="email" name="email" placeholder="you@email.com"/></div>
      <div class="r2">
        <div class="field"><label>DATE *</label><input type="date" name="date" id="bd" required/></div>
        <div class="field"><label>TIME SLOT *</label>
          <select name="time" required>
            <option value="">-- Select time --</option>
            <option>12:00 PM</option><option>1:00 PM</option><option>2:00 PM</option>
            <option>3:00 PM</option><option>4:00 PM</option><option>5:00 PM</option>
            <option>6:00 PM</option><option>7:00 PM</option><option>8:00 PM</option>
            <option>9:00 PM</option><option>10:00 PM</option>
          </select>
        </div>
      </div>
      <div class="r2">
        <div class="field"><label>NUMBER OF GUESTS *</label>
          <select name="guests" required>
            <option value="">-- Select --</option>
            <option>1 person</option><option>2 people</option><option>3 people</option>
            <option>4 people</option><option>5 people</option><option>6+ people</option>
          </select>
        </div>
        <div class="field"><label>SEATING PREFERENCE</label>
          <select name="seating">
            <option>No preference</option><option>Indoor</option><option>Outdoor</option>
          </select>
        </div>
      </div>
      <div class="field"><label>OCCASION</label>
        <select name="occasion">
          <option value="">-- None --</option>
          <option>Birthday</option><option>Anniversary</option>
          <option>Business Lunch</option><option>Family Gathering</option><option>Other</option>
        </select>
      </div>
      <div class="field"><label>SPECIAL REQUESTS</label>
        <textarea name="notes" placeholder="Dietary requirements, allergies, special decorations..."></textarea>
      </div>
      <button type="submit" class="sbtn">CONFIRM RESERVATION</button>
    </form>

    <?php if(isset($_GET['success'])): ?>
    <div class="success-msg" style="display:block">
      Your table has been reserved!<br>
      We will confirm your booking shortly via call or email.<br>
      We look forward to welcoming you to FLAMMA SKY BBQ &amp; Grill.
    </div>
    <?php elseif(isset($_GET['error'])): ?>
    <div class="error-msg" style="display:block">
      Something went wrong. Please try again or call us at +91 9642410067.
    </div>
    <?php endif; ?>
  </div>

  <div class="info-card">
    <div class="info-block">
      <h3>CONTACT US</h3>
      <div class="c-row">
        <div class="c-icon"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.14 12 19.79 19.79 0 0 1 1.08 3.4 2 2 0 0 1 3.06 1.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
        <div><div class="c-label">CALL US</div><div class="c-val">+91 9642410067</div></div>
      </div>
      <div class="c-row">
        <div class="c-icon"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
        <div><div class="c-label">EMAIL US</div><div class="c-val">flammaskyva@gmail.com</div></div>
      </div>
    </div>
    <div class="info-block">
      <h3>OPENING HOURS</h3>
      <div class="hours-item"><span class="day">MONDAY - SUNDAY</span><span class="time">12 PM – 11 PM</span></div>
      <h3>GOOD TO KNOW</h3>
      <div class="notes-box">
        &bull; Reservations held for 15 minutes<br>
        &bull; Private dining for groups 10+<br>
        &bull; Valet parking on weekends<br>
        &bull; Dietary requirements welcome
      </div>
    </div>
  </div>
</div>

<footer>
  <p>&copy; 2026 <span>FLAMMA SKY BBQ &amp; Grill</span> &nbsp;&middot;&nbsp;
  <a href="tel:+919642410067">+91 9642410067</a> &nbsp;&middot;&nbsp;
  <a href="mailto:flammaskyva@gmail.com">flammaskyva@gmail.com</a></p>
</footer>

<script>
  document.getElementById('bd').min = new Date().toISOString().split('T')[0];
</script>

</body>
</html>
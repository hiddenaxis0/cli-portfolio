<?php
// Minimal homepage for Sidney Franklin site
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sidney Franklin — Home</title>
  <style>
    body{font-family: Jost, sans-serif;background:#FAF7F2;color:#2A2420;margin:0}
    header{padding:28px 40px;display:flex;justify-content:space-between;align-items:center;background:rgba(250,247,242,0.96);border-bottom:1px solid #E8D8B0}
    .logo{font-family:Cinzel,serif;letter-spacing:.25em;text-transform:uppercase}
    nav a{margin-left:28px;text-decoration:none;color:#2A2420;font-size:.85rem}
    .hero{padding:140px 40px;text-align:center}
    .hero h1{font-family:Cormorant Garamond,serif;font-size:3rem;margin:0;color:#8B6E1E}
    .grid{display:flex;gap:24px;justify-content:center;margin-top:28px}
    .card{background:#fff;padding:20px 24px;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,0.04);min-width:200px}
    footer{padding:24px 40px;text-align:center;font-size:.85rem;color:#6B1E2E}
  </style>
</head>
<body>
  <header>
    <a class="logo" href="index.php">Sidney Franklin</a>
    <nav>
      <a href="bio.html">About</a>
      <a href="resume.html">Resume</a>
      <a href="reels.html">Reels</a>
      <a href="contact.html">Contact</a>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h1>Work that moves the story forward</h1>
      <p style="color:#2E2926;max-width:680px;margin:18px auto;line-height:1.6">Welcome — explore selected work, resume, reels, and contact for bookings or collaborations.</p>
      <div class="grid">
        <a class="card" href="reels.html">Reels</a>
        <a class="card" href="resume.html">Resume</a>
        <a class="card" href="contact.html">Contact</a>
      </div>
    </section>
  </main>

  <footer>
    © 2026 Sidney Franklin · All Rights Reserved
  </footer>
</body>
</html>
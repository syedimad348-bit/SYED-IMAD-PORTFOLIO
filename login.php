<?php
require 'db.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

$error = '';
if (isset($_POST['login'])) {
    $u = trim($_POST['username']);
    $p = trim($_POST['password']);
    if ($u === 'root' && $p === 'imad1234') {
        $_SESSION['user'] = $u;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Syed Imad POS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body, html { margin:0; padding:0; width:100%; height:100%; overflow:hidden; background:#0a0a0a; font-family:'Roboto', sans-serif; }
#bgCanvas { position:absolute; top:0; left:0; width:100%; height:100%; z-index:0; }
.login-card {
    position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
    width:420px; padding:50px 40px; border-radius:20px; 
    background: rgba(255,255,255,0.05); backdrop-filter: blur(18px);
    box-shadow: 0 12px 40px rgba(248,211,79,0.6); text-align:center; z-index:2;
}
h1 { color:#f8d34f; font-size:36px; margin-bottom:10px; text-shadow:0 0 12px rgba(248,211,79,0.7); }
.subtitle { color:#ccc; margin-bottom:30px; font-size:14px; }
/* Error Alert */
.alert { background: rgba(255,0,0,0.7); padding:12px; border-radius:10px; color:white; margin-bottom:20px; font-weight:bold; }
/* Inputs */
input { width:100%; padding:15px; margin-bottom:18px; border:none; border-radius:12px; font-size:16px; background: rgba(248,211,79,0.15); color:black; outline:none; font-weight:bold; transition:0.3s ease; }
input:focus { background: rgba(248,211,79,0.3); box-shadow:0 0 15px rgba(248,211,79,0.8); }
/* Button */
button { width:100%; padding:15px; border:none; border-radius:12px; font-size:18px; background:#f8d34f; color:black; cursor:pointer; font-weight:bold; transition:0.3s ease-in-out; box-shadow:0 6px 20px rgba(248,211,79,0.5); }
button:hover { background:#ffe278; transform:translateY(-2px); box-shadow:0 10px 28px rgba(248,211,79,0.7); }
</style>
</head>
<body>

<canvas id="bgCanvas"></canvas>

<div class="login-card">
    <h1>Syed Imad POS</h1>
    <p class="subtitle">Sign in to manage your restaurant</p>
    
    <?php if($error): ?>
        <div class="alert"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input name="username" placeholder="Username" required>
        <input name="password" type="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>
</div>

<script>
const canvas = document.getElementById('bgCanvas');
const ctx = canvas.getContext('2d');
let w = canvas.width = window.innerWidth;
let h = canvas.height = window.innerHeight;

window.addEventListener('resize', ()=>{ w=canvas.width=window.innerWidth; h=canvas.height=window.innerHeight; });

const particles = [];
const pCount = 150;
for(let i=0;i<pCount;i++){
    particles.push({
        x: Math.random()*w,
        y: Math.random()*h,
        r: Math.random()*3+1,
        dx: (Math.random()-0.5)*2,
        dy: (Math.random()-0.5)*2
    });
}

let mouse = {x: w/2, y: h/2};
window.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY; });

function draw(){
    ctx.clearRect(0,0,w,h);

    for(let i=0;i<particles.length;i++){
        const p = particles[i];

        // Mouse attraction
        let dx = mouse.x - p.x;
        let dy = mouse.y - p.y;
        let dist = Math.sqrt(dx*dx + dy*dy);
        if(dist < 200){
            p.x += dx*0.05;
            p.y += dy*0.05;
        }

        p.x += p.dx;
        p.y += p.dy;

        if(p.x<0||p.x>w) p.dx*=-1;
        if(p.y<0||p.y>h) p.dy*=-1;

        ctx.beginPath();
        ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
        ctx.fillStyle='rgba(248,211,79,0.9)';
        ctx.fill();

        // Connect lines
        for(let j=i+1;j<particles.length;j++){
            const p2 = particles[j];
            let dxl = p.x - p2.x;
            let dyl = p.y - p2.y;
            let d = Math.sqrt(dxl*dxl + dyl*dyl);
            if(d < 120){
                ctx.beginPath();
                ctx.moveTo(p.x,p.y);
                ctx.lineTo(p2.x,p2.y);
                ctx.strokeStyle='rgba(248,211,79,'+(1-d/120)*0.4+')';
                ctx.lineWidth = 1;
                ctx.stroke();
            }
        }
    }
    requestAnimationFrame(draw);
}
draw();
</script>

</body>
</html>

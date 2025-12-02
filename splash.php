<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Welcome</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
    body {
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: linear-gradient(135deg, #0a0a0a, #111111, #FFD700, #FFA500);
        font-family: "Segoe UI", sans-serif;
        overflow: hidden;
    }

    .container {
        text-align: center;
        animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .cartoon {
        width: 220px;
        cursor: pointer;
        transition: 0.5s;
        filter: drop-shadow(0px 8px 25px rgba(0,0,0,0.6));
    }

    /* Smile + blink animation */
    .cartoon.smile {
        transform: scale(1.2) rotate(10deg);
        filter: brightness(1.3) drop-shadow(0px 12px 30px rgba(0,0,0,0.7));
        animation: blink 0.8s;
    }

    @keyframes blink {
        0%, 100% { transform: scale(1.2) rotate(10deg); }
        50% { transform: scale(1.2) rotate(10deg) translateY(2px); }
    }

    h1 {
        color: gold;
        margin-top: 25px;
        font-size: 34px;
        letter-spacing: 1px;
    }

    p {
        color: #fff;
        margin-bottom: 25px;
        font-weight: 500;
        font-size: 16px;
    }
</style>
</head>

<body>

<div class="container">
    <p>Click the cartoon to continue</p>

    <!-- Local cartoon image -->
    <img src="images/doremon.png" id="cartoon" class="cartoon">

    <h1>Syed Imad POS</h1>
</div>

<script>
    const cartoon = document.getElementById("cartoon");

    cartoon.addEventListener("click", function() {
        cartoon.classList.add("smile");

        setTimeout(() => {
            window.location.href = "login.php";
        }, 800); // animation duration before redirect
    });
</script>

</body>
</html>

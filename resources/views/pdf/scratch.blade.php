<div style="text-align:center">

    <h3>اكشط الكرت 👇</h3>

    <div style="position:relative;width:300px;height:150px;margin:auto;background:#eee;">

        <div style="line-height:150px;font-size:22px;">
            {{ $card->code }}
        </div>

        <canvas id="scratch" style="position:absolute;top:0;left:0;"></canvas>
    </div>
</div>

<script>
const canvas = document.getElementById("scratch");
const ctx = canvas.getContext("2d");

canvas.width = 300;
canvas.height = 150;

ctx.fillStyle = "#999";
ctx.fillRect(0,0,300,150);

let drawing = false;

canvas.addEventListener("mousedown", () => drawing = true);
canvas.addEventListener("mouseup", () => drawing = false);

canvas.addEventListener("mousemove", (e) => {
    if(!drawing) return;

    const rect = canvas.getBoundingClientRect();

    ctx.globalCompositeOperation = "destination-out";
    ctx.beginPath();
    ctx.arc(e.clientX - rect.left, e.clientY - rect.top, 20, 0, Math.PI*2);
    ctx.fill();
});
</script>

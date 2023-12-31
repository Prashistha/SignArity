<?php
require 'config.php';
if(!empty($_SESSION["id"])){
  $id = $_SESSION["id"];
  $result = mysqli_query($conn, "SELECT * FROM tb_user WHERE id = $id");
  $row = mysqli_fetch_assoc($result);
}
else{
  header("Location: login.php");
}
?>

<!DOCTYPE html>
<html data-wf-page="625f1a727093c36474d2aed9" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <style>
      .wf-force-outline-none[tabindex="-1"]:focus {
        outline: none;
      }
    </style>
    <meta charset="utf-8" />
    <title>IOT Project</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link
      href="OpenCameramain.css"
      rel="stylesheet"
      type="text/css"
    />
    <link
      href="OpenCamerastyle.css"
      rel="stylesheet"
      type="text/css"
    />
    <style>
      video#live-cam {
        position: relative;
        transform: rotateY(180deg);
        max-width: 100%;
        -webkit-transform: rotateY(180deg);
        -moz-transform: rotateY(180deg);
      }
      #vid-container {
        border: 1px solid;
        width: fit-content;
        height: fit-content;
        margin: 0;
        padding: 0;
        position: relative;
      }

      #video-rect {
        border: 1px red solid;
        width: 60.5%;
        height: 55%;
        position: absolute;
        right: 0;
        bottom: 4px;
      }


    </style>
    <link
      rel="stylesheet"
      href="{{ url_for('static', filename='styles.css') }}"
      media="all"
    />
  </head>
  <body>
    <div class="section-3 wf-section">
      <div class="div-block">
        <h2 class="heading-14">Live Camera Preview</h2>
        <div class="div-block-2" id="vid-container">
          <video id="live-cam"></video>
          <div id="video-rect"></div>
        </div>
        <h3 class="heading-15">Current Snapshot</h3>
        <div class="div-block-3">
          <canvas id="image"></canvas>
        </div>
      </div>
      <div class="div-block div-block-12">
        <h2 class="heading-14 heading-16">Controls</h2>
        <div class="div-block-6">
          <div class="div-block-8">
            <div
              id="w-node-_4d7ead06-e94c-11f3-f394-98f06c51ba1d-74d2aed9"
              class="text-block"
            >
              Select your preferred voice:
            </div>
          </div>
          <div class="div-block-7">
            <div class="html-embed w-embed">
              <select name="voice" id="voice" style="width: 100% !important">
                <option value="male" selected="selected">male</option>
                <option value="female">female</option>
              </select>
            </div>
          </div>
        </div>
        <div class="div-block-9">
          <div class="div-block-4">
            <a id="start" class="button w-button">Start</a>
            <a id="stop" class="button-2 w-button">Pause</a
            ><a href="./" class="button-3 w-button">Refresh</a>
          </div>
          <div class="div-block-5">
            <button
              id="exportToText"
              onclick="download()"
              class="button-4 w-button"
            >
              Export to Text
            </button>
          </div>
        </div>
        <div class="div-block-15">
          <h3 class="heading-15">Manual Upload</h3>
          <div class="div-block-10">
            <div class="w-embed">
              <input
                type="file"
                name=""
                id="choose-file"
                accept="image/jpeg"
              />
            </div>
            <button id="upload" class="button-4 w-button">Upload Image</button>
            <button id="export_upload_result" style="visibility: hidden;">Export upload image result</button>
          </div>
        </div>
        <div class="div-block-13">
          <h3 class="heading-15">Output</h3>
          <div class="div-block-11">
            <div class="text-block-2">
              <div id="result"></div>
              <div id="upload-image-result"></div>
              <img src="" alt="" id="upload-image">
              <button style="visibility:hidden;" id="capture">Capture</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      const video = document.getElementById("live-cam");

      const start = document.getElementById("start");
      const capture = document.getElementById("capture");
      const upload_image_result = document.getElementById(
        "upload-image-result"
      );
      const export_upload_result = document.getElementById(
        "export_upload_result"
      );

      navigator.mediaDevices
        .getUserMedia({
          video: true,
          audio: false,
        })
        .then(function (stream) {
          video.srcObject = stream;
          video.play();
        });
      var stopLoop = false;
      async function imageCapture() {
        console.log("ruunong");
        const canvas = document.getElementById("image");
        const img = document.getElementById("testimg");

        canvas.width = video.clientWidth / 2;
        canvas.height = video.clientHeight / 2;
        const ctx = canvas.getContext("2d");
        ctx.scale(-1, 1);
        ctx.translate(-canvas.width, 0);

        //  ctx.drawImage(img,  sx,  sy,  sw, sh, dx, dy, dw,   dh); // draw video
        ctx.drawImage(
          video,
          -10,
          190,
          440,
          290,
          0,
          0,
          video.clientWidth / 2,
          video.clientHeight / 2
        );

        let image_data_url = canvas.toDataURL("image/jpeg");
        const _fetch = await fetch("/image", {
          method: "POST", // *GET, POST, PUT, DELETE, etc.
          mode: "cors", // no-cors, *cors, same-origin
          cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            image: "image eka",
            image_data: image_data_url,
          }),
        });
        const _result = await _fetch.json();
        console.log(_result);

        if (_result.status == 1) {
          document.getElementById("result").innerText += _result.value;
        }

        if (_result.value.length == 1) {
          let letter = _result.value;
          let voice = document.getElementById("voice").value;
          if (voice == "male") {
            letter = letter.toUpperCase();
            const audio = new Audio(`audio/${letter}_male.mp3`);
            audio.play();
          }
          if (voice == "female") {
            letter = letter.toUpperCase();
            const audio = new Audio(`audio/${letter}_female.mp3`);
            audio.play();
          }
        }

        var replyTimeout = setTimeout(() => {
          if (stopLoop == false) {
            imageCapture();
          }
        }, 1000);
      }

      function _start() {
        stopLoop = false;
        imageCapture();
      }
      start.addEventListener("click", _start);

      function download(filename, text) {
        filename = "text content";
        text = document.getElementById("result").innerText;
        var element = document.createElement("a");
        element.setAttribute(
          "href",
          "data:text/plain;charset=utf-8," + encodeURIComponent(text)
        );
        element.setAttribute("download", filename);

        element.style.display = "none";
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
      }
      export_upload_result.onclick = function () {
        filename = "text content uploaded image";
        text = export_upload_result.innerText;
        var element = document.createElement("a");
        element.setAttribute(
          "href",
          "data:text/plain;charset=utf-8," + encodeURIComponent(text)
        );
        element.setAttribute("download", filename);

        element.style.display = "none";
        document.body.appendChild(element);

        element.click();

        document.body.removeChild(element);
      };
      capture.onclick = () => {
        imageCapture();
      };
      const fileBrowser = document.getElementById("choose-file");
      function fileChange(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
          document.getElementById("upload-image").src = reader.result;
        };
      }
      fileBrowser.addEventListener("change", fileChange, false);
      const upload = document.getElementById("upload");

      async function imageUpload() {
        const image_data_url = document.getElementById("upload-image").src;
        const _fetch = await fetch("/image-upload", {
          method: "POST", // *GET, POST, PUT, DELETE, etc.
          cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            image: "image eka",
            image_data: image_data_url,
          }),
        });
        const _result = await _fetch.json();
        console.log(_result);
        upload_image_result.innerText += _result.value;

        if (_result.value.length == 1) {
          let letter = _result.value;
          let voice = document.getElementById("voice").value;
          if (voice == "male") {
            letter = letter.toUpperCase();
            const audio = new Audio(`audio/${letter}_male.mp3`);
            audio.play();
          }
          if (voice == "female") {
            letter = letter.toUpperCase();
            const audio = new Audio(`audio/${letter}_female.mp3`);
            audio.play();
          }
        }
      }

      upload.addEventListener("click", imageUpload, false);

      const stop = document.getElementById("stop");
      function _stop() {
        stopLoop = true;
      }
      stop.addEventListener("click", _stop, false);
    </script>
  </body>
</html>
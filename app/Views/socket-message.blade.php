@extends('layouts.app')
@section('style')
    <style>
      .msg-box{
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
      }
      #message-box p{
        font-family: cursive
      }
    </style>
@endsection
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card card-default rounded-0 shadow-sm mt-5">
        <div class="card-body" id="message-box"></div>
      </div>
    </div>
    <div class="msg-box">
      <div class="col-md-8 offset-md-2">
      <div class="card card-default rounded-0 shadow-sm mt-5">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="row m-5">
                <div class="col-12">
                  <input class="form-control rounded-0 mb-2" type="text" name="message" id="name"
                    placeholder="Your UserName">
                </div>
                <div class="col-6">
                  <button class="btn-block btn btn-outline-primary rounded-0" onclick="setUserName()">SET NAME</button>
                </div>
                <div class="col-6">
                  <button class="btn-block btn btn-outline-primary rounded-0"
                    onclick="setRandomUserName()">RANDOM</button>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <input class="form-control rounded-0 mb-2" type="text" name="message" id="message"
                placeholder="message..." required>
            </div>
            <div class="col-md-4">
              <button onclick="sendMessage()" class="mb-2 btn btn-block btn-outline-success rounded-0">send</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
  let UserName = undefined;

  $(function () {
    if (sessionStorage.getItem('Chat-UserName')) {
      $('#name').parent().parent().hide();

      UserName = sessionStorage.getItem('Chat-UserName');
    }
  })


  let socket = new WebSocket('ws://localhost:9000');

  socket.onopen = function (e) {

    console.log("Connection established!");
  };


  socket.onmessage = function (e) {
    let data = JSON.parse(e.data);

    console.log(data);

    $('#message-box').append(`
          <div class="row">
            <div class="col-6 text-left"><strong>${data.user}</strong></div>
            <div class="col-6 text-right text-danger text-secondary"><i>${data.time}</i></div>
            <p class="col-12 text-success p-2 shadow-sm mt-1 mb-1">${data.text}</p>
          </div>
        `);
  };


  function sendMessage() {

    if (UserName === undefined) {
      alert('Please set your name !');

      return false;
    }


    let message = $('input#message').val();

    if ($.trim(message) == '') {
      return false;
    }

    let data = {
      'user': UserName,
      'text': message,
      'time': today()
    }

    socket.send(JSON.stringify(data));

    $('#message-box').append(`
          <div class="row">
            <div class="col-6 text-left text-danger"><i>${data.time}</i></div>
            <div class="col-6 text-right text-secondary"><strong>${data.user}</strong></div>
            <p class="col-12 text-success text-right p-2 shadow-sm mt-1 mb-1">${data.text}</p>
          </div>
        `);
    $('input#message').val('');

    return false;
  }


  function today() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var hours = today.getHours();
    var minute = today.getMinutes();
    var second = today.getSeconds();

    return mm + '/' + dd + '/' + yyyy + ' ' + hours + ':' + minute + ':' + second;
  }


  function randomUserName(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }


  function setUserName() {
    if ($.trim($('#name').val()) != '') {
      UserName = $('#name').val();

      $('#name').parent().parent().hide();

      sessionStorage.setItem('Chat-UserName', UserName);
    }
  }


  function setRandomUserName() {
    $('#name').val(randomUserName(16));
  }
</script>
@endsection
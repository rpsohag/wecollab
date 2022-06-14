<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head></head>

<body>
  <style>
    @media print {
      *,
      :after,
      :before {
        color: #000!important;
        text-shadow: none!important;
        background: 0 0!important;
        -webkit-box-shadow: none!important;
        box-shadow: none!important;
      }
      a,
      a:visited {
        text-decoration: underline;
      }
      a[href]:after {
        content: " (" attr(href) ")";
      }
      a[href^="#"]:after,
      a[href^="javascript:"]:after {
        content: "";
      }
      h2,
      h3,
      p {
        orphans: 3;
        widows: 3;
      }
      h2,
      h3 {
        page-break-after: avoid;
      }
      .navbar {
        display: none;
      }
      .label {
        border: 1px solid #000;
      }
    }
    
    @media (min-width:768px) {
      .container {
        width: 750px;
      }
    }
    
    @media (min-width:992px) {
      .container {
        width: 970px;
      }
    }
    
    @media (min-width:1200px) {
      .container {
        width: 1170px;
      }
    }
    
    @media (min-width:992px) {
      .col-md-1,
      .col-md-10,
      .col-md-11,
      .col-md-12,
      .col-md-2,
      .col-md-3,
      .col-md-4,
      .col-md-5,
      .col-md-6,
      .col-md-7,
      .col-md-8,
      .col-md-9 {
        float: left;
      }
      .col-md-12 {
        width: 100%;
      }
      .col-md-11 {
        width: 91.66666667%;
      }
      .col-md-10 {
        width: 83.33333333%;
      }
      .col-md-9 {
        width: 75%;
      }
      .col-md-8 {
        width: 66.66666667%;
      }
      .col-md-7 {
        width: 58.33333333%;
      }
      .col-md-6 {
        width: 50%;
      }
      .col-md-5 {
        width: 41.66666667%;
      }
      .col-md-4 {
        width: 33.33333333%;
      }
      .col-md-3 {
        width: 25%;
      }
      .col-md-2 {
        width: 16.66666667%;
      }
      .col-md-1 {
        width: 8.33333333%;
      }
    }
    
    @media (min-width:768px) {
      .navbar {
        border-radius: 4px;
      }
    }
    
    @media (min-width:768px) {
      .navbar-header {
        float: left;
      }
    }
    
    @media (min-width:768px) {
      .navbar-collapse {
        width: auto;
        border-top: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
      }
      .navbar-collapse.collapse {
        display: block!important;
        height: auto!important;
        padding-bottom: 0;
        overflow: visible!important;
        visibility: visible!important;
      }
      .navbar-collapse.in {
        overflow-y: visible;
      }
    }
    
    @media (min-width:768px) {
      .container>.navbar-collapse,
      .container>.navbar-header {
        margin-right: 0;
        margin-left: 0;
      }
    }
    
    @media (min-width:768px) {
      .navbar-form {
        width: auto;
        padding-top: 0;
        padding-bottom: 0;
        margin-right: 0;
        margin-left: 0;
        border: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
      }
    }
    
    @media (min-width:768px) {
      .navbar-text {
        float: left;
        margin-right: 15px;
        margin-left: 15px;
      }
    }
    
    @media (min-width:768px) {
      .navbar-left {
        float: left!important;
      }
      .navbar-right {
        float: right!important;
        margin-right: -15px;
      }
      .navbar-right~.navbar-right {
        margin-right: 0;
      }
    }
    
    @media screen and (max-width: 700px) {
      .btn-header {
        display: none;
      }
    }
  </style>
  <div style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; background-color: #187ab39b; box-sizing: border-box;" align="center">{!! $header !!}</div>
  <br style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
  <!-- FORM Assistenza -->
  <section class="section-white clearfix" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; display: block;">
    <div class="container" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin-left: auto; margin-right: auto; padding-left: 15px; padding-right: 15px;">
      <div style="-moz-box-sizing: border-box; -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05); -webkit-box-sizing: border-box; background-color: #fff; border: 1px solid transparent; border-color: #d6e9c6; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.05); box-sizing: border-box; margin-bottom: 20px; margin-left: -15px; margin-right: -15px; text-align: center;"
        class="row panel panel-success">
        <div class="col-md-12 section-title" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
          <h2 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 30px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 20px; text-align: center;"><em style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">{{ $content['assistenza_per'] }}</strong></em></h2>
          <h3 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 24px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 20px; text-align: center;"><i class="text-primary" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #337ab7;"></i><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;"> Numero Ticket </strong><b style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;"><a href="{{ $content['link'] }}" target="_blank" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; background-color: transparent; box-sizing: border-box; color: #337ab7; text-decoration: none;">{{ $content['numero_ticket'] }}</a></b></h3>
          <br style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
        </div>
        <div class="col-md-4" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
          <label style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000; display: inline-block; font-weight: 700; margin-bottom: 5px; max-width: 100%;"><h4 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 18px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 10px;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">Nominativo</strong></h4></label>
          <p style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin: 0 0 10px;">{{ $content['nominativo'] }}</p>
          <label style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000; display: inline-block; font-weight: 700; margin-bottom: 5px; max-width: 100%;"><h4 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 18px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 10px;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">Oggetto</strong></h4></label>
          <p style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin: 0 0 10px;">{{ $content['oggetto'] }}</p>
        </div>
        <div class="col-md-4" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
          <label style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000; display: inline-block; font-weight: 700; margin-bottom: 5px; max-width: 100%;"><h4 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 18px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 10px;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">Area</strong></h4></label>
          <p style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin: 0 0 10px;">{{ $content['area'] }}</p>
          <label style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000; display: inline-block; font-weight: 700; margin-bottom: 5px; max-width: 100%;"><h4 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 18px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 10px;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">Motivo d'urgenza</strong></h4></label>
          <p style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin: 0 0 10px;"><span class="text-danger" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #a94442;">{{ $content['motivo_urgenza'] }}</span></p>
        </div>
        <div class="col-md-4" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
          <label style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000; display: inline-block; font-weight: 700; margin-bottom: 5px; max-width: 100%;"><h4 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 18px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 10px;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">Email</strong></h4></label>
          <p style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin: 0 0 10px;">{{ $content['email'] }}</p>
          <label style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000; display: inline-block; font-weight: 700; margin-bottom: 5px; max-width: 100%;"><h4 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 18px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 10px;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">Telefono</strong></h4></label>
          <p style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin: 0 0 10px;">{{ $content['numero'] }}</p>
        </div>
      </div>
      <br style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
      <div class="row panel panel-success" style="-moz-box-sizing: border-box; -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05); -webkit-box-sizing: border-box; background-color: #fff; border: 1px solid transparent; border-color: #d6e9c6; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.05); box-sizing: border-box; margin-bottom: 20px; margin-left: -15px; margin-right: -15px;">
        <div class="col-md-12" style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
          <br style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
          <label style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000; display: inline-block; font-weight: 700; margin-bottom: 5px; max-width: 100%;"><h4 style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: inherit; font-family: inherit; font-size: 18px; font-weight: 500; line-height: 1.1; margin-bottom: 10px; margin-top: 10px;"><strong style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; font-weight: 700;">Descrizione</strong></h4></label>
          <p style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; margin: 0 0 10px;">{{ $content['descrizione'] }}</p>
          <br style="-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;">
        </div>
      </div>
    </div>
  </section>
</body>

</html>
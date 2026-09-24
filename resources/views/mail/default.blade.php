/* DentalFlow — Tema de correo "Lagoon" (se inyecta inline vía CssToInlineStyles) */

/* Base */

body,
body *:not(html):not(style):not(br):not(tr):not(code) {
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif,
        'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    position: relative;
}

body {
    -webkit-text-size-adjust: none;
    background-color: #ffffff;
    color: #5c574d;
    height: 100%;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    width: 100% !important;
}

p,
ul,
ol,
blockquote {
    line-height: 1.4;
    text-align: start;
}

a {
    color: #157e73;
}

a img {
    border: none;
}

/* Typography */

h1 {
    color: #23282a;
    font-size: 18px;
    font-weight: bold;
    margin-top: 0;
    text-align: start;
}

h2 {
    color: #23282a;
    font-size: 16px;
    font-weight: bold;
    margin-top: 0;
    text-align: start;
}

h3 {
    color: #23282a;
    font-size: 14px;
    font-weight: bold;
    margin-top: 0;
    text-align: left;
}

p {
    font-size: 16px;
    line-height: 1.5em;
    margin-top: 0;
    text-align: left;
}

p.sub {
    font-size: 12px;
}

img {
    max-width: 100%;
}

/* Layout */

.wrapper {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #f4f1ea;
    margin: 0;
    padding: 0;
    width: 100%;
}

.content {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 0;
    padding: 0;
    width: 100%;
}

/* Header */

.header {
    padding: 25px 0;
    text-align: center;
}

.header a {
    color: #157e73;
    font-size: 19px;
    font-weight: bold;
    text-decoration: none;
}

/* Logo */

.logo {
    height: 75px;
    margin-top: 15px;
    margin-bottom: 10px;
    max-height: 75px;
    width: 75px;
}

/* Body */

.body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #f4f1ea;
    border-bottom: 1px solid #f4f1ea;
    border-top: 1px solid #f4f1ea;
    margin: 0;
    padding: 0;
    width: 100%;
}

.inner-body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    background-color: #ffffff;
    border-color: #e8e4da;
    border-radius: 12px;
    border-width: 1px;
    box-shadow: 0 1px 2px 0 rgba(35, 40, 42, 0.04), 0 10px 28px -20px rgba(20, 80, 75, 0.25);
    margin: 0 auto;
    padding: 0;
    width: 570px;
}

.inner-body a {
    word-break: break-all;
}

/* Subcopy */

.subcopy {
    border-top: 1px solid #e8e4da;
    margin-top: 25px;
    padding-top: 25px;
}

.subcopy p {
    font-size: 14px;
}

/* Footer */

.footer {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    margin: 0 auto;
    padding: 0;
    text-align: center;
    width: 570px;
}

.footer p {
    color: #948e82;
    font-size: 12px;
    text-align: center;
}

.footer a {
    color: #948e82;
    text-decoration: underline;
}

/* Tables */

.table table {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 30px auto;
    width: 100%;
}

.table th {
    border-bottom: 1px solid #e8e4da;
    margin: 0;
    padding-bottom: 8px;
}

.table td {
    color: #5c574d;
    font-size: 15px;
    line-height: 18px;
    margin: 0;
    padding: 10px 0;
}

.content-cell {
    max-width: 100vw;
    padding: 32px;
}

/* Buttons */

.action {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 30px auto;
    padding: 0;
    text-align: center;
    width: 100%;
    float: unset;
}

.button {
    -webkit-text-size-adjust: none;
    border-radius: 8px;
    color: #fff;
    display: inline-block;
    overflow: hidden;
    text-decoration: none;
}

.button-blue,
.button-primary {
    background-color: #1f9e8f;
    border-bottom: 8px solid #1f9e8f;
    border-left: 18px solid #1f9e8f;
    border-right: 18px solid #1f9e8f;
    border-top: 8px solid #1f9e8f;
}

.button-green,
.button-success {
    background-color: #178254;
    border-bottom: 8px solid #178254;
    border-left: 18px solid #178254;
    border-right: 18px solid #178254;
    border-top: 8px solid #178254;
}

.button-red,
.button-error {
    background-color: #b02f4a;
    border-bottom: 8px solid #b02f4a;
    border-left: 18px solid #b02f4a;
    border-right: 18px solid #b02f4a;
    border-top: 8px solid #b02f4a;
}

/* Panels */

.panel {
    border-left: #1f9e8f solid 4px;
    margin: 21px 0;
}

.panel-content {
    background-color: #f4f1ea;
    color: #5c574d;
    padding: 16px;
}

.panel-content p {
    color: #5c574d;
}

.panel-item {
    padding: 0;
}

.panel-item p:last-of-type {
    margin-bottom: 0;
    padding-bottom: 0;
}

/* Utilities */

.break-all {
    word-break: break-all;
}

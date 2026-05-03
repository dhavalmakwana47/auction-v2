<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        /* Change font family */
        background: linear-gradient(135deg, #f9f9f9 50%, #e0e0e0 100%);
        /* Gradient background */
        margin: 0;
        padding: 0;
        color: #333;
    }

    h1,
    h2 {
        color: #b90e63;
        /* Maintain your color scheme */
        margin: 0;
    }

    section {
        padding: 80px 20px;
        /* Increased padding for more space */
    }

    a,
    a:hover,
    a:focus,
    a:active {
        text-decoration: none;
        outline: none;
        transition: all 0.3s ease;
        /* Smooth transitions for hover effects */
    }

    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .error-page-area {
        background: white;
        /* White background for content */
        border-radius: 8px;
        /* Rounded corners */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        /* Subtle shadow effect */
        padding: 40px;
        /* Inner padding */
    }

    .error-page-area h1 {
        font-size: 10vw;
        /* Responsive font size */
        font-weight: 900;
        line-height: 120px;
        margin-bottom: 20px;
    }

    .error-page-area h2 {
        font-weight: 600;
        text-transform: capitalize;
        margin-bottom: 20px;
        /* Space below heading */
    }

    .error-page-area a {
        margin: 15px 5px 0;
    }

    .btn-theme.btn-md {
        background: #b90e63;
        /* Button background */
        color: #fff;
        border: none;
        /* No border */
        border-radius: 5px;
        /* Rounded corners */
        padding: 12px 30px;
        /* Padding for button */
        font-size: 16px;
        /* Font size for button */
        transition: background 0.3s ease;
        /* Transition effect for hover */
    }

    .btn-theme.btn-md:hover {
        background: #a40c4f;
        /* Darker shade on hover */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        /* Shadow on hover */
    }
</style>

<body>

    <section class="error-page-area text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <h1>404</h1>
                    <h2>Sorry, the page was not found!</h2>
                    <a class="btn btn-theme effect btn-md" href="/">Back to Home</a>
                </div>
            </div>
        </div>
    </section>

</body>

</html>

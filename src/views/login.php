<body>
    <h1>ログインフォーム</h1>
    <form action="/?process=process_login" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit">ログイン</button>
    </form>
    <a href="/?page=home">ホームへ</a>
</body>
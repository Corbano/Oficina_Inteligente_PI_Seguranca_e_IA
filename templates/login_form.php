<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-lg-9 col-xl-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-6 bg-bic-blue text-white d-none d-md-flex flex-column justify-content-center align-items-center p-5 rounded-start">
                            <i class="bi bi-gear-wide-connected" style="font-size: 6rem;"></i>
                            <h2 class="fw-bold mt-3">Oficina</h2>
                            <h2 class="fw-light">Inteligente</h2>
                        </div>

                        <div class="col-md-6 p-5">
                            <h3 class="text-center fw-light mb-4">Acessar Sistema</h3>
                            <?php if (isset($_GET['error'])): ?>
                                <div class="alert alert-danger text-center small p-2">Usuário ou senha inválidos.</div>
                            <?php endif; ?>
                            <form action="index.php?action=do_login" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <div class="form-floating mb-3">
                                    <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuário" required>
                                    <label for="usuario">Usuário:</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Senha" required>
                                    <label for="senha">Senha:</label>
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-lg text-white bg-bic-blue">Entrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
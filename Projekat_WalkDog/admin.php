<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DogWalk – Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="admin.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark navbar-expand-lg custom-header">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">DogWalk Admin</a>

        <div class="d-flex align-items-center gap-3 text-white">
            <span>Administrator</span>
            <a href="#" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 bg-white border-end min-vh-100 p-3">
            <ul class="nav flex-column nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" href="#overview">
                        <i class="bi bi-grid"></i> Pregled
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#walkers">
                        <i class="bi bi-person-badge"></i> Šetači
                        <span class="badge bg-danger ms-2">2</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#users">
                        <i class="bi bi-people"></i> Korisnici
                    </a>
                </li>
            </ul>
        </div>

        <!-- MAIN -->
        <div class="col-md-9 col-lg-10 p-4">

            <!-- STATS -->
            <h3 class="mb-4">Pregled sistema</h3>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-people fs-2"></i>
                            <h4>128</h4>
                            <p class="text-muted">Korisnici</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-person-badge fs-2"></i>
                            <h4>34</h4>
                            <p class="text-muted">Šetači</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-hourglass-split fs-2"></i>
                            <h4>2</h4>
                            <p class="text-muted">Na čekanju</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-activity fs-2"></i>
                            <h4>312</h4>
                            <p class="text-muted">Šetnje</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WALKERS -->
            <h4 class="mb-3">Upravljanje šetačima</h4>

            <div class="row mb-3">
                <div class="col-md-6">
                    <input class="form-control" placeholder="Pretraži...">
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option>Svi statusi</option>
                        <option>Pending</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive mb-5">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Šetač</th>
                        <th>Email</th>
                        <th>Ocena</th>
                        <th>Šetnje</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr class="table-warning">
                        <td>Mila Nikolić</td>
                        <td>mila@email.com</td>
                        <td>-</td>
                        <td>0</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td>
                            <button class="btn btn-success btn-sm">
                                <i class="bi bi-check"></i>
                            </button>
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-x"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>Ana Marković</td>
                        <td>ana@email.com</td>
                        <td>4.5</td>
                        <td>47</td>
                        <td><span class="badge bg-success">Aktivan</span></td>
                        <td>
                            <button class="btn btn-secondary btn-sm">
                                Deaktiviraj
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- USERS -->
            <h4 class="mb-3">Upravljanje korisnicima</h4>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                    <tr>
                        <th>Korisnik</th>
                        <th>Email</th>
                        <th>Registrovan</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td>Jovana Milić</td>
                        <td>jovana@email.com</td>
                        <td>2025</td>
                        <td><span class="badge bg-success">Aktivan</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm">
                                Zaključaj
                            </button>
                        </td>
                    </tr>

                    <tr class="table-secondary">
                        <td>Nikola Stanković</td>
                        <td>nikola@email.com</td>
                        <td>2025</td>
                        <td><span class="badge bg-secondary">Zaključan</span></td>
                        <td>
                            <button class="btn btn-success btn-sm">
                                Otključaj
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
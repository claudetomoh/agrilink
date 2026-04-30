<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'AgriLink') ?> | AgriLink Ghana</title>
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <!-- Tailwind CSS with AgriLink design tokens -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            "primary":                   "#2c694e",
            "primary-dim":               "#1e5d43",
            "primary-container":         "#b1f0ce",
            "on-primary":                "#e1ffec",
            "on-primary-container":      "#1d5c42",
            "secondary":                 "#126c4a",
            "secondary-container":       "#a1f4c8",
            "on-secondary-container":    "#005e3e",
            "tertiary":                  "#3f6750",
            "tertiary-container":        "#cbf9db",
            "on-tertiary-container":     "#38614a",
            "surface":                   "#f7f9ff",
            "background":                "#f7f9ff",
            "surface-container-lowest":  "#ffffff",
            "surface-container-low":     "#eff4fc",
            "surface-container":         "#e7eff8",
            "surface-container-high":    "#dfe9f5",
            "surface-container-highest": "#d7e4f2",
            "surface-dim":               "#ccdcec",
            "on-surface":                "#28343e",
            "on-surface-variant":        "#54606c",
            "outline":                   "#6f7c88",
            "outline-variant":           "#a6b3c1",
            "error":                     "#9f403d",
            "error-container":           "#fe8983",
          },
          fontFamily: {
            headline: ['Manrope', 'system-ui', 'sans-serif'],
            body:     ['Inter', 'system-ui', 'sans-serif'],
          },
          borderRadius: {
            'DEFAULT': '0.5rem',
            'lg':      '1rem',
            'xl':      '1.5rem',
            '2xl':     '2rem',
            '3xl':     '2.5rem',
            'full':    '9999px',
          },
        },
      },
    };
  </script>
  <!-- App CSS -->
  <link rel="stylesheet" href="<?= APP_URL ?>/css/app.css?v=<?= filemtime(BASE_PATH.'/public/css/app.css') ?>">
  <link rel="stylesheet" href="<?= APP_URL ?>/css/icons.css">
  <?php if (!empty($extraCSS)) foreach ($extraCSS as $css): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; ?>
</head>
<body>

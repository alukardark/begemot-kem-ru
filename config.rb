require 'compass/import-once/activate'
# require 'ceaser-easing'

project_path = "local/templates/axioma/"
http_path = "/local/templates/axioma/"
css_dir = "/"
sass_dir = "/assets/_sass"
images_dir = "/local/templates/axioma/assets/_images"
javascripts_dir = "/local/templates/axioma/assets/_js"
fonts_dir = "/local/templates/axioma/assets/_fonts"

environment = :development
output_style = (environment == :production) ? :compressed : :expanded
sourcemap = (environment == :production) ? false : true
line_comments = (environment == :production) ? false : true

# asset_cache_buster = :none
# cache = false

relative_assets = true
# composer install
echo ">>>>> Running composer install..."
composer install

# cleaning up previous dist
echo ">>>>> Removing up previous disted package..."
rm -rf dist

# make bin/main executable
echo ">>>>> chmod +x bin/main..."
chmod +x bin/main

# download phar-composer
if [ ! -f phar-composer.phar ]; then
    echo ">>>>> phar-composer.phar not found. Downloading..."
    wget https://github.com/clue/phar-composer/releases/download/v1.0.0/phar-composer.phar
else
    echo ">>>>> Found phar-composer.phar..."
fi

# create dist directory
echo ">>>>> Creating dist directory..."
mkdir -p dist

# run the build
echo ">>>>> Disting..."
php phar-composer.phar build . dist

echo ">>>>> dist/Recommendation.phar disted"

RED='\033[0;31m'
NC='\033[0m' # No Color
echo ">>>>> To run it: ${RED}php dist/Recommendation.phar <genre> <time>${NC}"
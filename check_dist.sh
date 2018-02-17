RED="\033[0;31m";
GREEN="\033[0;32m";
NC="\033[0m";

echo "\n\n";
DIST_FILE="dist/Recommendation.phar";
if [ -f $DIST_FILE ]; then
  echo "${GREEN}Successfully disting $DIST_FILE${NC}";
  exit 0;
else
  echo "${RED}Failed to dist $DIST_FILE${NC}";
  exit 1;
fi
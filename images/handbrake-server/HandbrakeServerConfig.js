var HandbrakeServerConfig = {}

HandbrakeServerConfig.main = {};

HandbrakeServerConfig.main.rootFolder = "/handbrake/input";
HandbrakeServerConfig.main.outputFolder = "/handbrake/output";
HandbrakeServerConfig.main.listenPort = 8181;
HandbrakeServerConfig.main.handbrakeExit = "Encode done!";

HandbrakeServerConfig.profiles = {
  "fire-tv": "-i {inputFile} -o {outputFile}.mkv --encoder x264 --quality 20.0 --two-pass --aencoder ffac3,ffac3 --ab 160,448 --mixdown dpl2,5_2_lfe --arate Auto,Auto --drc 1.0,1.0 --subtitle scan,1,2,3,4 --subtitle-forced --native-language eng --format mkv --large-file --decomb --loose-anamorphic --modulus 2 --markers --x264-preset veryslow --x264-profile film --h264-profile high --h264-level 4.0",
  "high-profile":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -a 1,1 -E copy:ac3,faac -B 160,160 -6 auto,dpl2 -R Auto,Auto -D 0.0,0.0 -f mp4 --detelecine --decomb --loose-anamorphic -m -x b-adapt=2:rc-lookahead=50",
  "android-tablet":"-i {inputFile} -o {outputFile}.m4v  -e x264 -q 20.0 -r 30 --pfr  -a 1 -E faac -B 160 -6 dpl2 -R Auto -D 0.0 -f mp4 -4 -X 1280 --loose-anamorphic -m",
  "universal":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -a 1,1 -E faac,copy:ac3 -B 160,160 -6 dpl2,auto -R Auto,Auto -D 0.0,0.0 -f mp4 -X 720 --loose-anamorphic -m -x cabac=0:ref=2:me=umh:bframes=0:weightp=0:8x8dct=0:trellis=0:subme=6",
  "ipod":"-i {inputFile} -o {outputFile}.mp4  -e x264 -b 700 -a 1 -E faac -B 160 -6 dpl2 -R Auto -D 0.0 -f mp4 -I -X 320 -m -x level=30:bframes=0:weightp=0:cabac=0:ref=1:vbv-maxrate=768:vbv-bufsize=2000:analyse=all:me=umh:no-fast-pskip=1:subme=6:8x8dct=0:trellis=0",
  "iphone+ipod-touch":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -a 1 -E faac -B 128 -6 dpl2 -R Auto -D 0.0 -f mp4 -X 480 -m -x cabac=0:ref=2:me=umh:bframes=0:weightp=0:subme=6:8x8dct=0:trellis=0",
  "iphone-4":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -r 29.97 --pfr  -a 1 -E faac -B 160 -6 dpl2 -R Auto -D 0.0 -f mp4 -4 -X 960 --loose-anamorphic -m",
  "ipad":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -r 29.97 --pfr  -a 1 -E faac -B 160 -6 dpl2 -R Auto -D 0.0 -f mp4 -4 -X 1024 --loose-anamorphic -m",
  "apple-tv":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -a 1,1 -E faac,copy:ac3 -B 160,160 -6 dpl2,auto -R Auto,Auto -D 0.0,0.0 -f mp4 -4 -X 960 --loose-anamorphic -m -x cabac=0:ref=2:me=umh:b-pyramid=none:b-adapt=2:weightb=0:trellis=0:weightp=0:vbv-maxrate=9500:vbv-bufsize=9500",
  "apple-tv-2":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -a 1,1 -E faac,copy:ac3 -B 160,160 -6 dpl2,auto -R Auto,Auto -D 0.0,0.0 -f mp4 -4 -X 1280 --loose-anamorphic -m",
  "normal":"-i {inputFile} -o {outputFile}.mp4  -e x264 -q 20.0 -a 1 -E faac -B 160 -6 dpl2 -R Auto -D 0.0 -f mp4 --strict-anamorphic -m -x ref=2:bframes=2:subme=6:mixed-refs=0:weightb=0:8x8dct=0:trellis=0",
  "classic":"-i {inputFile} -o {outputFile}.mp4  -b 1000 -a 1 -E faac -B 160 -6 dpl2 -R Auto -D 0.0 -f mp4"
};

module.exports = HandbrakeServerConfig;

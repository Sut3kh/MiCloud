FROM node:4.8.4

# Install Handbrake CLI.
RUN apt-get update \
  && apt-get install -y HandBrake-cli \
  && apt-get clean

# Download && install handbrake server.
RUN cd /usr/local \
  && git clone https://github.com/Sut3kh/handbrake-server.git \
  && cd handbrake-server \
  && npm install

# Copy config override.
ADD HandbrakeServerConfig.js /usr/local/handbrake-server/HandbrakeServerConfig.js

# Run it.
WORKDIR /usr/local/handbrake-server
CMD node app.js

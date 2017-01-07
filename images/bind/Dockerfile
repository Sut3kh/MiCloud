FROM centos:7.3.1611

# Update centos and install Bind (and initscripts for /usr/libexec/generate-rndc-key.sh)
RUN yum update -y \
  && yum install -y bind initscripts \
  && yum clean all

# Generate rndc key for Bind.
RUN /usr/libexec/generate-rndc-key.sh

# Copy run script.
COPY named-start.sh /usr/local/bin

# Copy Bind Config.
COPY etc /etc
RUN chown named:named /etc/named.conf

# Expose DNS ports.
EXPOSE 53/udp 53/tcp

# Run named by default.
CMD ["/usr/local/bin/named-start.sh"]


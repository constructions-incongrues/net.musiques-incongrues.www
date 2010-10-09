from fabric import contrib
from fabric.api import *
import ConfigParser
import os
import fnmatch
import shutil

# TODO : should be able to deploy from freshly cloned sources or from current workdir

def configure(configfile):
  "Reads external configuration in configfile"

  config = ConfigParser.SafeConfigParser()
  config.read(configfile)
  env.config = config

def prepare():
  "Prepares sources prior to deployment."

  # Make sure configuration is set
  require('config', provided_by=[configure])

  print('Preparing sources for deployment')

  # Find -dist files and perform strings substitution
  # TODO : factor dedicated function
  config_flat = _config_flatten(env.config)
  dist_files = _find_files(os.getcwd(), '*-dist')
  print '> Replacing %d tokens in %d files' % (len(config_flat), len(dist_files))
  for file in dist_files:
    distributed_file = _undist(file)
    shutil.copyfile(file, distributed_file)
    print '  - %s' % distributed_file
    for directive, value in config_flat:
      f_dest = open(distributed_file, 'r')
      contents = f_dest.read()
      f_dest.close()
      f_dest = open(distributed_file, 'w')
      f_dest.write(contents.replace('@%s@' % directive, value))
      f_dest.close()
  print '> Done replacing tokens'

def deploy():
  "Deploys sources to remote server and run appropriate remote commands."

  # Make sure configuration is set
  require('config', provided_by=[configure])

  print('Deploying to remote server')
  prepare()

  # TODO : take care of excludes
  contrib.project.rsync_project(remote_dir=env.config.get('paths', 'install'), exclude=[], delete=True, local_dir='%s/*' % os.getcwd(), extra_opts='--exclude-from=%s/etc/rsync.excludes' % os.getcwd())

def symlinks():
  # Make sure configuration is set
  require('config', provided_by=[configure])

  # Get base paths
  install = env.config.get('paths', 'install')
  webroot = env.config.get('paths', 'webroot')

  # Create symlinks
  run('ln -sf %s/forum %s/forum' % (install, webroot))
  run('ln -sf %s/sfproject/web %s/forum/s' % (install, webroot))
  run('ln -sf %s/web/* %s/' % (install, webroot))

def reload_db():
  
  # TODO : POTENTIALLY DESTRUCTIVE ACTION. ASK FOR CONFIRMATION

  # Make sure configuration is set
  require('config', provided_by=[configure])

  run('mysql -u%s -p%s %s < %s' % (env.config.get('database_vanilla', 'user'), env.config.get('database_vanilla', 'password'), env.config.get('database_vanilla', 'name'), '%s/data/sql/vanilla.sql' % env.config.get('paths', 'install')))
  run('mysql -u%s -p%s %s < %s' % (env.config.get('database_vanilla', 'user'), env.config.get('database_vanilla', 'password'), env.config.get('database_vanilla', 'name'), '%s/data/sql/vanilla.styles.sql' % env.config.get('paths', 'install')))
  run('mysql -u%s -p%s %s < %s' % (env.config.get('database_asaph', 'user'), env.config.get('database_asaph', 'password'), env.config.get('database_asaph', 'name'), '%s/data/sql/asaph.sql' % env.config.get('paths', 'install')))

def clear_cache():
  # Make sure configuration is set
  require('config', provided_by=[configure])

  run('cd %s/sfproject && ./symfony cc' % env.config.get('paths', 'install'))

def gather_remote_variators():
  # Make sure configuration is set
  require('config', provided_by=[configure])

  get('%s/forum/extensions/PageMng/CustomPages.php' % env.config.get('paths', 'install'), '%s/forum/extensions/PageMng/CustomPages.php' % os.getcwd())

# -- HELPERS

def _find_files(directory, pattern):
  found = []
  for root, dirs, files in os.walk(directory):
    for file in files:
      if fnmatch.fnmatch(file, pattern):
        found.append('%s/%s' % (root, file))

  return found

def _config_flatten(config):
  config_flat = []
  for section in config.sections():
    for key, value in config.items(section):
      config_flat.append(('%s.%s' % (section.upper(), key.upper()), value))

  return config_flat

def _undist(file):
  return file.split('-dist')[0]

import sys
sys.path.append('bin/epaphrodites/python/config/')
from initJsonLoader import InitJsonLoader

class hello_world:

    def print_hello_world(self):
        print('Hello welcome to epaphrodites from Python!')

if __name__ == '__main__':  
    hello_world_instance = hello_world()
    hello_world_instance.print_hello_world()  
        
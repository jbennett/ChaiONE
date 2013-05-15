//
//  ViewController.m
//  ADN Firehose
//
//  Created by Jonathan Bennett on 2013-05-14.
//  Copyright (c) 2013 Jonathan Bennett. All rights reserved.
//

#import "ViewController.h"
#import "Post.h"
#import "PostCell.h"
#import "UIImage+ImageNames.h"

@interface ViewController ()

@property (nonatomic, strong) NSMutableDictionary *imageCache;
@property (nonatomic, assign) int highwater;

- (IBAction)refreshPosts:(id)sender;

@end

@implementation ViewController

- (NSArray *)posts
{
    if (!_posts) {
        _posts = @[];
    }
    
    return _posts;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    self.imageCache = [NSMutableDictionary dictionary];
    self.highwater = 0;
    
    self.refreshControl = [[UIRefreshControl alloc] init];
    [self.refreshControl addTarget:self action:@selector(refreshPosts:) forControlEvents:UIControlEventValueChanged];
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    [self refreshPosts:nil];
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    Post *p = self.posts[indexPath.row];
    
    return MAX(89, [self sizeForText:p.text].height + 35.0f); // 35 is magic from storyboard
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return self.posts.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static CGFloat radius = 35.0f; // circle == maximum rounded-ness
    PostCell *cell = (PostCell *)[tableView dequeueReusableCellWithIdentifier:@"Post Cell" forIndexPath:indexPath];
    
    Post *p = self.posts[indexPath.row];
    cell.username.text = p.username;
    cell.text.text = p.text;
    
    // simple image cache. This would be better if saved out to tmp on disc or something possible
    // TODO: make this more robust
    // FIXME: if two+ posts are by the same user, this will trigger multiple downloads until one returns, then it will be cached
    if (self.imageCache[p.username]) {
        cell.image.image = self.imageCache[p.username];
    } else {
        UIImage *defaultImage = [self roundedImage:[UIImage defaultUserImage] withRadius:radius];
        cell.image.image = defaultImage;
        
        dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
            NSURL *imageURL = p.imageURL;
            
            __block NSData *imageData;
            
            dispatch_sync(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
                imageData = [NSData dataWithContentsOfURL:imageURL];
                UIImage *tempImage = [UIImage imageWithData:imageData];
                
                if (tempImage) {
                    self.imageCache[p.username] = [self roundedImage:tempImage withRadius:radius];
                
                    dispatch_sync(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
                        cell.image.image = self.imageCache[p.username];
                    });
                } else {
                    NSLog(@"Could not download: %@", imageURL);
                    // don't try to re-download profile pic
                    self.imageCache[p.username] = [self roundedImage:defaultImage withRadius:radius];
                }
            });
        });
    }
    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
}


#pragma mark - Actions
- (void)refreshPosts:(id)sender
{
    NSURL *url = [NSURL URLWithString:[self globalURL]];

    dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
        NSData *JSONData = [NSData dataWithContentsOfURL:url];
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:JSONData
                                                             options:kNilOptions
                                                               error:nil]; // FIXME: skipped error checking
      
        if ([json[@"data"] count]) {
            self.highwater = [json[@"meta"][@"max_id"] integerValue]; // update highwater mark
            NSLog(@"%d", self.highwater);
            
            // prepend new posts
            // we are assuming that setting the highwater mark ensures ADN does not send duplicates
            NSMutableArray *posts = [[Post createArray:json[@"data"]] mutableCopy];
            NSIndexPath *position; // where should we scroll to
            if (self.posts.count == 0) { // bottom of list on first load
                 position = [NSIndexPath indexPathForItem:posts.count-1 inSection:0];
            } else {
                // stay where you currently are, move down the number of rows you added
                position = [NSIndexPath indexPathForItem:posts.count inSection:0];
            }
            [posts addObjectsFromArray:self.posts];

            dispatch_async(dispatch_get_main_queue(), ^{
                self.posts = posts;
                [self.tableView reloadData];
                [self.tableView scrollToRowAtIndexPath:position atScrollPosition:UITableViewScrollPositionTop animated:NO];
            });
        }
        
        [self.refreshControl endRefreshing];
    });
}

#pragma mark - Private
- (CGSize)sizeForText:(NSString *)text
{
    UIFont *font = [UIFont systemFontOfSize:17.0];
    
    return [text sizeWithFont:font constrainedToSize:CGSizeMake(216.0f, MAXFLOAT)]; // 216 is a magic number from the storyboard
}

- (UIImage *)roundedImage:(UIImage *)image withRadius:(float)radius
{
    CGRect imageRect = CGRectMake(0, 0, 70.0, 70.0);
    
    UIGraphicsBeginImageContextWithOptions(imageRect.size, NO, 0.0f); // use device scale factor
        UIBezierPath *path = [UIBezierPath bezierPathWithRoundedRect:imageRect cornerRadius:radius];
        [path addClip];
        [image drawInRect:imageRect];
        UIImage *maskedImage = UIGraphicsGetImageFromCurrentImageContext();
    UIGraphicsEndImageContext();
    
    return maskedImage;
}
                  
- (NSString *)globalURL
{
    return [NSString stringWithFormat:@"https://alpha-api.app.net/stream/0/posts/stream/global?since_id=%d", self.highwater];
}

@end

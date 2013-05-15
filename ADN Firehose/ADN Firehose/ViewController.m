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

@interface ViewController ()

@property (nonatomic, strong) NSMutableDictionary *imageCache;

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
    
    return MAX(89, [self sizeForText:p.text].height + 14.0f); // 14 is magic from storyboard
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return self.posts.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    PostCell *cell = (PostCell *)[tableView dequeueReusableCellWithIdentifier:@"Post Cell" forIndexPath:indexPath];
    
    Post *p = self.posts[indexPath.row];
    cell.username.text = p.username;
    cell.text.text = p.text;
    
    // simple self-rolled image cache. This would be better if saved out to tmp on disc or something possible
    // TODO: make this more robust
    if (self.imageCache[p.username]) {
        cell.image.image = self.imageCache[p.username];
    } else {
        dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
            NSURL *imageURL = p.imageURL;
            
            __block NSData *imageData;
            
            dispatch_sync(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
                imageData = [NSData dataWithContentsOfURL:imageURL];
                
                self.imageCache[p.username] = [UIImage imageWithData:imageData];
                
                dispatch_sync(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
                    cell.image.image = self.imageCache[p.username];
                });
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
    NSURL *url = [NSURL URLWithString:@"https://alpha-api.app.net/stream/0/posts/stream/global"];

    dispatch_async(dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0), ^{
        NSData *JSONData = [NSData dataWithContentsOfURL:url];
        NSDictionary *json = [NSJSONSerialization JSONObjectWithData:JSONData
                                                             options:kNilOptions
                                                               error:nil]; // skip error checking

        if (json[@"data"]) {
            NSArray *posts = [Post createArray:json[@"data"]];

            dispatch_async(dispatch_get_main_queue(), ^{
                self.posts = posts;
                [self.tableView reloadData];
            });
        }
        
        [self.refreshControl endRefreshing];
    });
}

#pragma mark - Private
- (CGSize)sizeForText:(NSString *)text
{
    UIFont *font = [UIFont systemFontOfSize:17.0];
    
    return [text sizeWithFont:font constrainedToSize:CGSizeMake(216.0f, MAXFLOAT)]; // 216 is a magic number for the stpryboard
}

@end

//
//  Post.m
//  ADN Firehose
//
//  Created by Jonathan Bennett on 2013-05-14.
//  Copyright (c) 2013 Jonathan Bennett. All rights reserved.
//

#import "Post.h"

@implementation Post

+ (id)initWithDictionary:(NSDictionary *)values
{
    return [[self alloc] initWithDictionary:values];
}

+ (id)createArray:(NSArray *)objects
{
    NSMutableArray *posts = [NSMutableArray arrayWithCapacity:objects.count];
    
    [objects enumerateObjectsUsingBlock:^(id obj, NSUInteger idx, BOOL *stop) {
        Post *p = [Post initWithDictionary:obj];
        [posts addObject:p];
    }];
    
    return posts;
}

- (id)initWithDictionary:(NSDictionary *)values
{
    if (self = [super init]) {
        if (values[@"user"][@"username"]) {
            _username = values[@"user"][@"username"];
        }
        
        if (values[@"text"]) {
            _text = values[@"text"];
        }
        
        if (values[@"user"][@"avatar_image"][@"url"]) {
            _imageURL = [NSURL URLWithString:values[@"user"][@"avatar_image"][@"url"]];
        }
        
    }
    
    return self;
}

@end

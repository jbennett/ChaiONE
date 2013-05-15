//
//  Post.h
//  ADN Firehose
//
//  Created by Jonathan Bennett on 2013-05-14.
//  Copyright (c) 2013 Jonathan Bennett. All rights reserved.
//

#import "MTLModel.h"

@interface Post : NSObject

@property(nonatomic, strong) NSString *username;
@property(nonatomic, strong) NSString *text;
@property(nonatomic, strong) NSURL *imageURL;
@property(nonatomic, strong) UIImage *image;

+ (id)initWithDictionary:(NSDictionary *)values;
+ (id)createArray:(NSArray *)objects;

- (id)initWithDictionary:(NSDictionary *)values;

@end
